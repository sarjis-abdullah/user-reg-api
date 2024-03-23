<?php

namespace App\Http\Controllers;

use App\Exports\ProductStockExport;
use App\Http\Requests\Product\BatchUploadRequest;
use App\Http\Requests\Product\IndexRequest;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductStockResource;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Stock;
use App\Repositories\Contracts\BranchRepository;
use App\Repositories\Contracts\BrandRepository;
use App\Repositories\Contracts\CategoryRepository;
use App\Repositories\Contracts\CompanyRepository;
use App\Repositories\Contracts\DepartmentRepository;
use App\Repositories\Contracts\ProductRepository;
use App\Repositories\Contracts\StockRepository;
use App\Repositories\Contracts\SubCategoryRepository;
use App\Repositories\Contracts\SubDepartmentRepository;
use App\Repositories\Contracts\TaxRepository;
use App\Repositories\Contracts\UnitRepository;
use App\Services\Helpers\CsvHelper;
use App\Services\Helpers\PdfHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\MpdfException;
use PDF;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * ProductController constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $products = $this->productRepository->findBy($request->all());

        $productResources = ProductResource::collection($products['products']);

        $productResources->additional(Arr::except($products, ['products']));

        return $productResources;
    }

    /**
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function productStock(IndexRequest $request): AnonymousResourceCollection
    {
        $products = $this->productRepository->stocks($request->all());

        $productResources = ProductStockResource::collection($products['products']);

        $productResources->additional(Arr::except($products, ['products']));

        return $productResources;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function getProductByExpirationDate(IndexRequest $request): AnonymousResourceCollection
    {
        $request->merge(['expiredEndDate' => $request->get('endDate', null), 'expiredStartDate' => $request->get('startDate', null)]);

        $products = $this->productRepository->getProductByExpirationDate($request->all());

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return ProductResource
     */
    public function store(StoreRequest $request)
    {
        $product = $this->productRepository->save($request->all());

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return ProductResource
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Product $product
     * @return ProductResource
     */
    public function update(UpdateRequest $request, Product $product)
    {
        $product = $this->productRepository->update($product, $request->all());

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function destroy(Product $product)
    {
        $this->productRepository->delete($product);

        return response()->json(null, 204);
    }

    /**
     * @return JsonResponse
     */
    public function generateProductBarcodeNumber(): JsonResponse
    {
        $number = mt_rand(1000000, 9999999); // better than rand()

        // call the same function if the barcode exists already
        if ($this->barcodeNumberExists($number)) {
            return $this->generateProductBarcodeNumber();
        }

        // otherwise, it's valid and can be used
        return response()->json(['barcode' => $number], 200);
    }

    /**
     * Batch upload products by importing a CSV file.
     *
     * @param BatchUploadRequest $request
     * @return JsonResponse
     */
    public function batchUploadCsvFile(BatchUploadRequest $request)
    {
        $fields = [
            'name', 'barcode', 'categoryId', 'category', 'subCategory', 'subCategoryId',
            'companyId', 'company', 'brandId', 'brand', 'unitId', 'unit', 'discountId',
            'taxId', 'status', 'branchId', 'sku', 'quantity', 'alertQuantity', 'unitCost',
            'unitPrice', 'expiredDate', 'tax', 'type', 'genericName', 'department','subDepartment'
        ];

        $rows = CsvHelper::getRows($request->file('fileSource'), $fields);

        $stockRepository = app(StockRepository::class);

        $rows->each(function (array $data) use ($stockRepository) {
            // Filter out empty values or zeros
            $data = array_filter($data, fn ($value) => !empty($value) || $value == 0);

            if (isset($data['barcode'])) {
                $data['barcode'] = preg_replace('/\s+/', ' ', trim($data['barcode']));
            }

            $product = $this->findOrCreateProduct($data);

            $branchRepository = app(BranchRepository::class);
            $branch = $branchRepository->findOne($data['branchId']);

            if (isset($data['quantity']) && $branch) {
                $sku = Purchase::generateSku($product->name, $product->id, $data['unitPrice']);

                $existStock = $stockRepository->findOneBy([
                    'sku' => $sku,
                    'branchId' => $data['branchId'],
                    'unitCost' => $data['unitCost'],
                    'unitPrice' => $data['unitPrice'],
                ]);

                if ($existStock){
                    $existStock->update([
                        'quantity' => ($existStock->quantity + $data['quantity']),
                    ]);
                }else{
                    $stockRepository->save([
                        'productId' => $product->id,
                        'branchId' => $data['branchId'] ?? null,
                        'sku' => $data['sku'] ?? Purchase::generateSku($product->name, $product->id, $data['unitPrice']) ?? 0,
                        'quantity' => $data['quantity'] ?? 0,
                        'alertQuantity' => $data['alertQuantity'] ?? 100,
                        'unitCost' => $data['unitCost'] ?? 0,
                        'unitPrice' => $data['unitPrice'] ?? 0,
                        'expiredDate' => $data['expiredDate'] ?? null,
                        'status' => $data['status'] ?? Stock::STATUS_AVAILABLE,
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Products have been successfully imported.'], 201);
    }

    /**
     * Find or create a product based on the provided data.
     *
     * @param array $data
     * @return Product
     */
    private function findOrCreateProduct(array $data)
    {
        $product = $this->productRepository->getModel()
            ->when(isset($data['barcode']), function ($query) use ($data) {
                $query->where('barcode', $data['barcode']);
            })
            ->withTrashed()
            ->first();

        if ($product instanceof Product) {
            // Check if the product is soft-deleted and restore it
            if ($product->trashed()) {
                $product->restore();
            }

            if (!empty($data['category'])) {
                $categoryRepository = app(CategoryRepository::class);
                $category = $categoryRepository->createOrGetCategoryByName($data['category']);
                $data['categoryId'] = $category->id;
                unset($data['category']);
            }

            if (!empty($data['subCategory'])) {
                $subCategoryRepository = app(SubCategoryRepository::class);
                if (isset($data['categoryId'])) {
                    $subCategory = $subCategoryRepository->createOrGetSubCategoryByName($data['subCategory'], $data['categoryId']);
                    $data['subCategoryId'] = $subCategory->id;
                }

                unset($data['subCategory']);
            }

            if (!empty($data['unit'])) {
                $unitRepository = app(UnitRepository::class);
                $unit = $unitRepository->createOrGetUnitByName($data['unit']);
                $data['unitId'] = $unit->id;
                unset($data['unit']);
            }

            if (!empty($data['company'])) {
                $companyRepository = app(CompanyRepository::class);
                $company = $companyRepository->createOrGetCompanyByName($data['company']);
                $data['companyId'] = $company->id;
                unset($data['company']);
            }

            if (!empty($data['brand']) && !empty($data['companyId'])) {
                $brandRepository = app(BrandRepository::class);
                $brand = $brandRepository->createOrGetBrandByName($data['brand'], $data['companyId']);
                $data['brandId'] = $brand->id;
                $data['companyId'] = $brand->companyId;
                unset($data['brand']);
            }

            if (!empty($data['tax'])) {
                $taxRepository = app(TaxRepository::class);
                $tax = $taxRepository->createOrGetTaxByAmountAndType($data['tax'], 'percentage');
                $data['taxId'] = $tax->id;
                unset($data['tax']);
                unset($data['type']);
            }

            if (!empty($data['department'])) {
                $departmentRepository = app(DepartmentRepository::class);
                $department = $departmentRepository->createOrGetDepartmentByName($data['department']);
                $data['departmentId'] = $department->id;
                unset($data['department']);
            }

            if (!empty($data['departmentId']) && !empty($data['subDepartment'])) {
                $subDepartmentRepository = app(SubDepartmentRepository::class);
                $subDepartment = $subDepartmentRepository->createOrGetSubDepartmentByName($data['subDepartment'], $data['departmentId']);
                $data['subDepartmentId'] = $subDepartment->id;
                $data['departmentId'] = $subDepartment->department_id;
                unset($data['subDepartment']);
            }

            $this->productRepository->update($product, $data, false);
        } else {
            if (!empty($data['tax'])) {
                $taxRepository = app(TaxRepository::class);
                $tax = $taxRepository->createOrGetTaxByAmountAndType($data['tax'], 'percentage');
                $data['taxId'] = $tax->id;
                unset($data['tax']);
                unset($data['type']);
            }

            if (!empty($data['department'])) {
                $departmentRepository = app(DepartmentRepository::class);
                $department = $departmentRepository->createOrGetDepartmentByName($data['department']);
                $data['departmentId'] = $department->id;
                unset($data['department']);
            }

            if (!empty($data['departmentId']) && !empty($data['subDepartment'])) {
                $subDepartmentRepository = app(SubDepartmentRepository::class);
                $subDepartment = $subDepartmentRepository->createOrGetSubDepartmentByName($data['subDepartment'], $data['departmentId']);
                $data['subDepartmentId'] = $subDepartment->id;
                $data['departmentId'] = $subDepartment->department_id;
                unset($data['subDepartment']);
            }

            // Generate a barcode if none exists
            $data['barcode'] = $this->generateUniqueBarcode($data['barcode']);

            $product = $this->productRepository->save($data);
        }

        return $product;
    }

    /**
     * Generate a unique barcode if none exists.
     *
     * @param string|null $barcode
     * @return string
     */
    private function generateUniqueBarcode(?string $barcode)
    {
        if (empty($barcode)) {
            do {
                $barcode = mt_rand(1000000, 9999999);
            } while ($this->barcodeNumberExists($barcode));
        } elseif ($this->barcodeNumberExists($barcode)) {
            do {
                $barcode = mt_rand(1000000, 9999999);
            } while ($this->barcodeNumberExists($barcode));
        }

        return $barcode;
    }

    /**
     * Check if a barcode number exists in the database.
     *
     * @param string $barcode
     * @return bool
     */
    private function barcodeNumberExists(string $barcode): bool
    {
        return $this->productRepository->getModel()->where('barcode', $barcode)->exists();
    }


    /**
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function getArchiveData(IndexRequest $request): AnonymousResourceCollection
    {
        $products = $this->productRepository->findBy( $request->all(), false, true);

        return ProductResource::collection($products);
    }

    /**
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function getArchivedProductAndProductWithArchiveStocks(IndexRequest $request): AnonymousResourceCollection
    {
        $products = $this->productRepository->getArchivedProductAndProductWithArchiveStocks( $request->all());

        return ProductResource::collection($products);
    }

    /**
     * @param IndexRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function downloadProductPdf(IndexRequest $request): StreamedResponse
    {
        $products = $this->productRepository->findBy($request->all());

        $productResources = ProductResource::collection($products['products']);

        $productResources->additional(Arr::except($products, ['products']));

        return PdfHelper::downloadPdf($productResources, 'pdf.product.productList', 'Product-list');
    }

    /**
     * @param IndexRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function productStockPdf(IndexRequest $request)
    {
        $products = $this->productRepository->stocks($request->all());

        $productResources = ProductStockResource::collection($products['products']);

        $productResources->additional(Arr::except($products, ['products']));

        return PdfHelper::downloadPdf(json_encode($productResources), 'pdf.reports.stockReport', 'Product-stock-report.pdf');
    }

    /**
     * @param IndexRequest $request
     * @return BinaryFileResponse
     */
    public function productStockExcel(IndexRequest $request): BinaryFileResponse
    {
        $products = $this->productRepository->stocks($request->all());

        $productResources = ProductStockResource::collection($products['products']);

        $productResources->additional(Arr::except($products, ['products']));

        return Excel::download(new ProductStockExport($productResources), 'Product-Stock.xlsx');
    }

    /**
     * @param $id
     * @return ProductResource
     */
    public function restore($id): ProductResource
    {
        $restoreProduct = $this->productRepository->restore($id);

        return new ProductResource($restoreProduct);
    }

}
