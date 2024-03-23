<?php

namespace App\Http\Controllers\WC;

use App\Http\Controllers\Controller;
use App\Http\Requests\WC\OrderRequest;
use App\Http\Requests\WC\ProductRequest;
use App\Http\Requests\WC\StockRequest;
use App\Http\Resources\WC\OrderResource;
use App\Http\Resources\WC\ProductResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class WCApiController extends Controller
{
    const DEFAULT_PAGINATION = 20;
    const DEFAULT_ORDER_BY = 'id';
    const DEFAULT_ORDER_DIR = 'desc';

    public $product;

    public $order;

    public $defaultConfig;


    /**
     * @param Product $product
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(Product $product, Order $order)
    {
        $this->product = $product;
        $this->defaultConfig = $this->defaultConfig();
        $this->order = $order;
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function defaultConfig(): array
    {
        return [
            'pagination' => request()->get('per_page', self::DEFAULT_PAGINATION),
            'orderBy' => request()->get('order_by', self::DEFAULT_ORDER_BY),
            'orderDir' => request()->get('order_direction', self::DEFAULT_ORDER_DIR),
        ];
    }

    /**
     * Getting product list.
     *
     * @param ProductRequest $request
     * @return AnonymousResourceCollection
     */
    public function products(ProductRequest $request): AnonymousResourceCollection
    {
        $products = $this->product
            ->newQuery()
            ->with([
                'stocksForWc.branch', 'productVariations', 'company', 'category',
                'subCategory', 'unit', 'tax', 'discount', 'brand', 'barcodeImage',
                'image', 'createdByUser', 'updatedByUser'
            ])
            ->when($request->filled('id'), fn($query) => $query->where('id', $request->id))
            ->when($request->filled('barcode'), fn($query) => $query->where('barcode', $request->barcode))
            ->when($request->filled('createdByUserId'), fn($query) => $query->where('createdByUserId', $request->createdByUserId))
            ->when($request->filled('categoryId'), fn($query) => $query->where('categoryId', $request->categoryId))
            ->when($request->filled('subCategoryId'), fn($query) => $query->where('subCategoryId', $request->subCategoryId))
            ->when($request->filled('companyId'), fn($query) => $query->where('companyId', $request->companyId))
            ->when($request->filled('brandId'), fn($query) => $query->where('brandId', $request->brandId))
            ->when($request->filled('query'), fn($query) => $query->where('name', 'like', "%{$request->get('query')}%")
                ->orWhere('barcode', 'like', "%{$request->get('query')}%")
                ->orWhereHas('stocksForWc', fn($query) => $query->where('sku', 'like', "%{$request->get('query')}%")
                    ->orWhereHas('branch', fn($query) => $query->where('name', 'like', "%{$request->get('query')}%")))
                ->orWhereHas('company', fn($query) => $query->where('name', 'like', "%{$request->get('query')}%"))
                ->orWhereHas('category', fn($query) => $query->where('name', 'like', "%{$request->get('query')}%"))
                ->orWhereHas('unit', fn($query) => $query->where('name', 'like', "%{$request->get('query')}%"))
                ->orWhereHas('brand', fn($query) => $query->where('name', 'like', "%{$request->get('query')}%"))
            )
            ->orderBy($this->defaultConfig['orderBy'], $this->defaultConfig['orderDir'])
            ->paginate($this->defaultConfig['pagination']);

        return ProductResource::collection($products);
    }

    /**
     * Getting specific product.
     *
     * @param $id
     * @return ProductResource
     */
    public function product($id): ProductResource
    {
        $product = $this->product
            ->newQuery()
            ->with([
                'stocksForWc.branch', 'productVariations', 'company', 'category',
                'subCategory', 'unit', 'tax', 'discount', 'brand', 'barcodeImage',
                'image', 'createdByUser', 'updatedByUser'
            ])
            ->where('id', $id)
            ->first();

        return new ProductResource($product);
    }

    /**
     * Getting order list.
     *
     * @param OrderRequest $request
     * @return AnonymousResourceCollection
     */
    public function orders(OrderRequest $request): AnonymousResourceCollection
    {
        $orders = $this->order
            ->newQuery()
            ->with([
                "coupon", "company", "branch", "customer", "salePerson",
                "orderProducts.product", "orderProducts.productReturns",
                "payments", "invoiceImage", 'createdByUser'
            ])
            ->when($request->filled('id'), fn($query) => $query->where('id', $request->id))
            ->when($request->filled('createdByUserId'), fn($query) => $query->where('createdByUserId', $request->createdByUserId))
            ->when($request->filled('companyId'), fn($query) => $query->where('companyId', $request->companyId))
            ->when($request->filled('branchId'), fn($query) => $query->where('branchId', $request->branchId))
            ->when($request->filled('referenceId'), fn($query) => $query->where('referenceId', $request->referenceId))
            ->when($request->filled('salePersonId'), fn($query) => $query->where('salePersonId', $request->salePersonId))
            ->when($request->filled('customerId'), fn($query) => $query->where('customerId', $request->customerId))
            ->when($request->filled('couponId'), fn($query) => $query->where('couponId', $request->couponId))
            ->when($request->filled('invoice'), fn($query) => $query->where('invoice', $request->invoice))
            ->when($request->filled('terminal'), fn($query) => $query->where('terminal', $request->terminal))
            ->when($request->filled('amount'), fn($query) => $query->where('amount', $request->amount))
            ->when($request->filled('tax'), fn($query) => $query->where('tax', $request->tax))
            ->when($request->filled('discount'), fn($query) => $query->where('discount', $request->discount))
            ->when($request->filled('roundOffAmount'), fn($query) => $query->where('roundOffAmount', $request->roundOffAmount))
            ->when($request->filled('shippingCost'), fn($query) => $query->where('shippingCost', $request->shippingCost))
            ->when($request->filled('paid'), fn($query) => $query->where('paid', $request->paid))
            ->when($request->filled('due'), fn($query) => $query->where('due', $request->due))
            ->when($request->filled('deliveryMethod'), fn($query) => $query->where('deliveryMethod', $request->deliveryMethod))
            ->when($request->filled('paymentStatus'), fn($query) => $query->where('paymentStatus', $request->paymentStatus))
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->status))
            ->when($request->filled('updatedByUserId'), fn($query) => $query->where('updatedByUserId', $request->updatedByUserId))
            ->when($request->filled('startDate') && $request->filled('endDate'), fn($query) => $query->whereBetween('date', [$request->startDate, $request->endDate]))
            ->when($request->filled('query'),
                fn($query) => $query->where('referenceId', 'like', "%{$request->query}%")
                ->orWhere('invoice', 'like', "%{$request->query}%")
                ->orWhere('terminal', 'like', "%{$request->query}%")
            )
            ->orderBy($this->defaultConfig['orderBy'], $this->defaultConfig['orderDir'])
            ->paginate($this->defaultConfig['pagination']);

        return OrderResource::collection($orders);
    }

    /**
     * Getting specific order.
     *
     * @param $id
     * @return OrderResource
     */
    public function order($id): OrderResource
    {
        $order = $this->order
            ->newQuery()
            ->with([
                "coupon", "company", "branch", "customer", "salePerson",
                "orderProducts.product", "orderProducts.productReturns",
                "payments", "invoiceImage", 'createdByUser'
            ])
            ->where('id', $id)
            ->first();

        return new OrderResource($order);
    }

    /**
     * Update Specific Product Stock
     *
     * @param StockRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function stock(StockRequest $request): JsonResponse
    {
        $stock = Stock::query()
            ->where('productId', $request->productId)
            ->where('sku', $request->sku)
            ->first();

        if (!$stock){

            throw ValidationException::withMessages([
                'productId' => 'No Stock found with this Product Id',
                'sku' => 'No Stock found with this sku',
            ]);
        }

        try {

            $prevQty = $stock->quantity;

            $profitAmount = $stock->unitProfit * $request->saleQuantity;

            DB::beginTransaction();

            $stock->update([
                'quantity' => ($prevQty - $request->saleQuantity)
            ]);

            (new StockLog())->fill([
                'stockId' => $stock->id,
                'productId' => $request->productId,
                'resourceId' => null,
                'type' => StockLog::TYPE_PLAT_FORM_SALE,
                'prevQuantity' => $prevQty,
                'newQuantity' => $request->saleQuantity,
                'quantity' => $stock->quantity,
                'profitAmount' => $profitAmount,
                'date' => now(),
            ])->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Stock Update Successful'], 200);

        }catch (\Exception $exception){
            return response()->json(['success' => false, 'message' => $exception->getMessage()], $exception->getCode());
        }
    }
}
