<?php


namespace App\Repositories;


use App\Events\Product\ProductCreatedEvent;
use App\Events\Product\ProductOpeningStockCreatedEvent;
use App\Events\Product\ProductUpdatedEvent;
use App\Events\Product\ProductVariantsCreatedEvent;
use App\Events\Woocommerce\ProductSavingEvent;
use App\Models\Branch;
use App\Models\Bundle;
use App\Models\Category;
use App\Models\Company;
use App\Models\Discount;
use App\Models\Offer;
use App\Models\OfferProduct;
use App\Models\OfferPromoterProduct;
use App\Models\Product;
use App\Models\OrderProduct;
use App\Models\OrderProductReturn;
use App\Models\Stock;
use App\Models\Tax;
use App\Models\Unit;
use App\Repositories\Contracts\BrandRepository;
use App\Repositories\Contracts\CategoryRepository;
use App\Repositories\Contracts\CompanyRepository;
use App\Repositories\Contracts\ProductRepository;
use App\Repositories\Contracts\StockRepository;
use App\Repositories\Contracts\SubCategoryRepository;
use App\Repositories\Contracts\UnitRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class EloquentProductRepository extends EloquentBaseRepository implements ProductRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false, $onlyTrashed = false)
    {
        $queryBuilder = $this->model;

        if (isset($searchCriteria['onlyBundle'])) {
            $queryBuilder = $queryBuilder->whereNotNull('bundleId');
            unset($searchCriteria['onlyBundle']);
        }
        if (isset($searchCriteria['endDate'])) {
            $queryBuilder = $queryBuilder->whereDate('created_at', '<=', Carbon::parse($searchCriteria['endDate']));
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder = $queryBuilder->whereDate('created_at', '>=', Carbon::parse($searchCriteria['startDate']));
            unset($searchCriteria['startDate']);
        }

        if (isset($searchCriteria['branchId']) && isset($searchCriteria['quantity'])) {
            $queryBuilder = $queryBuilder->hasBranchAndQuantity($searchCriteria['branchId'], $searchCriteria['quantity']);

            $queryBuilder->withCount('stocks');
            $queryBuilder->having('stocks_count', '>', 0);
            unset($searchCriteria['branchId'], $searchCriteria['quantity']);

        } else {
            if (isset($searchCriteria['branchId'])) {
                $queryBuilder = $queryBuilder->hasBranch($searchCriteria['branchId'], $onlyTrashed);
                unset($searchCriteria['branchId']);
            }

            if (isset($searchCriteria['quantity'])) {
                $queryBuilder = $queryBuilder->hasQuantity($searchCriteria['quantity']);
                unset($searchCriteria['quantity']);
            }
        }

        if (isset($searchCriteria['acceptWithoutStock'])) {
            unset($searchCriteria['acceptWithoutStock']);
        }

        if (isset($searchCriteria['havingStockAlertQuantity'])) {
            unset($searchCriteria['havingStockAlertQuantity']);
        }

        $withSummary = false;
        if (!empty($searchCriteria['withSummary'])) {
            unset($searchCriteria['withSummary']);
            $withSummary = true;
        }

        $searchCriteria = $this->applyFilterInProductSearch($searchCriteria, $onlyTrashed);

        $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';

        if ($orderBy == 'quantity') {
            $queryBuilder = $queryBuilder->withCount(['stocks as totalStockQuantity' => function ($query) {
                $query->select(DB::raw('COALESCE(sum(quantity),0) as totalStockQuantity'));
            }]);

            $queryBuilder = $queryBuilder->orderBy('totalStockQuantity', $orderDirection);
        } else {
            $queryBuilder->orderBy($orderBy, $orderDirection);
        }

        if ($withTrashed) {
            $queryBuilder->withTrashed();
        }
        if ($onlyTrashed) {
            $queryBuilder->onlyTrashed();
        }

        //  to show in the summary
        $queryBuilder
            ->withSum(
                ['stocks' => function ($query) {
                    $query->where('quantity', '>', 0);
                }],
                'unitPrice'
            )
            ->withSum(
                ['stocks' => function ($query) {
                    $query->where('quantity', '>', 0);
                }],
                'unitCost'
            )
            ->withCount([
                'stocks as totalStockQuantity' => function ($query) {
                    $query->where('quantity', '>', 0)
                        ->select(DB::raw('COALESCE(sum(quantity),0) as totalStockQuantity'));
                },
                'stocks as totalStockValue' => function ($query) {
                    $query->where('quantity', '>', 0)
                        ->select(DB::raw('COALESCE(sum(quantity * unitCost), 0) as totalStockValue'));
                },
            ])
            ->withCount('stocks as variations');

        $summary = [];
        if ($withSummary) {
            $allData = $queryBuilder->get();
            $summary['totalStockQuantity'] = round($allData->sum('totalStockQuantity'), 2);
            $summary['totalStockValue'] = round($allData->sum('totalStockValue'), 2);
            $summary['totalSalePrice'] = round($allData->sum('stocks_sum_unit_price'), 2);
            $summary['totalPurchasePrice'] = round($allData->sum('stocks_sum_unit_cost'), 2);

        }

        if (empty($searchCriteria['withoutPagination'])) {
            $page = !empty($searchCriteria['page']) ? (int)$searchCriteria['page'] : 1;
            $products = $queryBuilder->paginate($limit, ['*'], 'page', $page);
        } else {
            $products = $queryBuilder->get();
        }

        $pageWiseSummary = [];

        $pageWiseSummary['totalStockQuantity'] = round($products->sum('totalStockQuantity'), 2);
        $pageWiseSummary['totalStockValue'] = round($products->sum('totalStockValue'), 2);
        $pageWiseSummary['totalSalePrice'] = round($products->sum('stocks_sum_unit_price'), 2);
        $pageWiseSummary['totalPurchasePrice'] = round($products->sum('stocks_sum_unit_cost'), 2);

        if ($withSummary) {
            return ['products' => $products, 'summary' => $summary, 'pageWiseSummary' => $pageWiseSummary];
        }

        return ['products' => $products, 'pageWiseSummary' => $pageWiseSummary];
    }


    /**
     * @param $searchCriteria
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function stocks($searchCriteria): array
    {
        Cache::flush();

        $products = $this->model
            ->newQuery()
            ->with([
                'category', 'company', 'subCategory', 'brand', 'image', 'createdByUser', 'updatedByUser',
                'stocks.branch', 'stocks.orderProducts', 'stocks.productReturned'
            ])
            ->when(isset($searchCriteria['query']), function ($q) use ($searchCriteria) {
                $q->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                    ->orWhereHas('stocks', function ($q) use ($searchCriteria) {
                        $q->where('sku', 'like', '%' . $searchCriteria['query'] . '%');
                    });
            })
            ->whereHas('stocks',
                fn($q) => $q->where('quantity', '>', 0)
                    ->when(isset($searchCriteria['branchId']),
                        fn($q) => $q->where('branchId', $searchCriteria['branchId'])))
            ->orderBy('id', 'desc')
            ->withCount([
                'stocks as totalStockQuantity' => function ($query) use ($searchCriteria) {
                    $query->where('quantity', '>', 0)
                        ->when(isset($searchCriteria['branchId']), fn($q) => $q->where('branchId', $searchCriteria['branchId']))
                        ->select(DB::raw('COALESCE(sum(quantity),0) as totalStockQuantity'));
                },
                'stocks as totalStockValue' => function ($query) use ($searchCriteria) {
                    $query->where('quantity', '>', 0)
                        ->when(isset($searchCriteria['branchId']), fn($q) => $q->where('branchId', $searchCriteria['branchId']))
                        ->select(DB::raw('COALESCE(sum(quantity * unitCost), 0) as totalStockValue'));
                },
                'stocks as totalStockSaleValue' => function ($query) use ($searchCriteria) {
                    $query->where('quantity', '>', 0)
                        ->when(isset($searchCriteria['branchId']), fn($q) => $q->where('branchId', $searchCriteria['branchId']))
                        ->select(DB::raw('COALESCE(sum(quantity * unitPrice), 0) as totalStockValue'));
                },
               /* 'stocks as totalSalePrice' => function ($query) use ($searchCriteria) {
                    $query->where('quantity', '>', 0)
                        ->when(isset($searchCriteria['branchId']), fn($q) => $q->where('branchId', $searchCriteria['branchId']))
                        ->select(DB::raw('COALESCE(sum(unitPrice), 0) as totalSalePrice'));
                },*/
                /*'stocks as totalPurchasePrice' => function ($query) use ($searchCriteria) {
                    $query->where('quantity', '>', 0)
                        ->when(isset($searchCriteria['branchId']), fn($q) => $q->where('branchId', $searchCriteria['branchId']))
                        ->select(DB::raw('COALESCE(sum(unitCost), 0) as totalPurchasePrice'));
                },*/
            ]);


        /*
         * Summary section start.
         */

        $stocksQuery = Stock::query()
            ->where('stocks.quantity', '>', 0)
            ->whereIn('stocks.productId', $products->pluck('id')->toArray())
            ->when(isset($searchCriteria['branchId']), fn($q) => $q->where('stocks.branchId', $searchCriteria['branchId']));

        /*$orderProductQuery = OrderProduct::query()
            ->whereIn('order_products.stockId', $stocksQuery->pluck('id')->toArray())
            ->select(
                DB::raw('SUM(order_products.quantity) as totalOrderProductQty'),
                DB::raw('SUM(order_product_returns.quantity) as totalOrderReturnQty'),
            )
            ->leftJoin('order_product_returns', 'order_product_returns.orderProductId', '=', 'order_products.id')
            ->first();*/

        $stocks = $stocksQuery->select(
            DB::raw('sum(stocks.quantity) as totalStockQuantity'),
            DB::raw('sum(stocks.quantity * stocks.unitCost) as totalStockValue'),
            DB::raw('sum(stocks.quantity * stocks.unitPrice) as totalStockSaleValue'),
            /*DB::raw('sum(stocks.unitPrice) as totalSalePrice'),
            DB::raw('sum(stocks.unitCost) as totalPurchasePrice'),*/
        )->first();

        $summary['totalStockQuantity'] = round($stocks->totalStockQuantity, 2);
        $summary['totalStockValue'] = round($stocks->totalStockValue, 2);
        $summary['totalStockSaleValue'] = round($stocks->totalStockSaleValue, 2);
        /*$summary['totalSalePrice'] = round($stocks->totalSalePrice, 2);
        $summary['totalPurchasePrice'] = round($stocks->totalPurchasePrice, 2);*/
        /*$summary['totalSoldQuantity'] = round(($orderProductQuery->totalOrderProductQty - $orderProductQuery->totalOrderReturnQty), 2);*/

        /*
         * Summary section End.
         */

        if (isset($searchCriteria['withoutPagination'])) {
            $paginateData = $products->get();
        } else {
            $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
            $page = !empty($searchCriteria['page']) ? (int)$searchCriteria['page'] : 1;
            $paginateData = $products->paginate($limit, ['*'], 'page', $page);
        }

        /*
         * Page wise summary section start.
         */

        /*$stocksPageWiseQuery = Stock::query()
            ->where('stocks.quantity', '>', 0)
            ->whereIn('stocks.productId', $paginateData->pluck('id')->toArray())
            ->when(isset($searchCriteria['branchId']), fn($q) => $q->where('stocks.branchId', $searchCriteria['branchId']));*/

        /*$orderProductPageWiseQuery = OrderProduct::query()
            ->whereIn('order_products.stockId', $stocksPageWiseQuery->pluck('id')->toArray())
            ->select(
                DB::raw('SUM(order_products.quantity) as orderProductQty'),
                DB::raw('SUM(order_product_returns.quantity) as orderProductReturnQty'),
            )
            ->leftJoin('order_product_returns', 'order_product_returns.orderProductId', '=', 'order_products.id')
            ->first();*/

        $pageWiseSummary['totalStockQuantity'] = round($paginateData->sum('totalStockQuantity'), 2);
        $pageWiseSummary['totalStockValue'] = round($paginateData->sum('totalStockValue'), 2);
        $pageWiseSummary['totalStockSaleValue'] = round($paginateData->sum('totalStockSaleValue'), 2);
        /*$pageWiseSummary['totalSalePrice'] = round($paginateData->sum('totalSalePrice'), 2);
        $pageWiseSummary['totalPurchasePrice'] = round($paginateData->sum('totalPurchasePrice'), 2);*/
        /*$pageWiseSummary['totalSoldQuantity'] = round(($orderProductPageWiseQuery->orderProductQty - $orderProductPageWiseQuery->orderProductReturnQty), 2);*/

        /*
         * Page wise summary section end.
         */

        return ['products' => $paginateData, 'summary' => $summary, 'pageWiseSummary' => $pageWiseSummary];
    }


    /**
     * @param array $searchCriteria
     * @return mixed
     */
    public function getProductByExpirationDate(array $searchCriteria = [])
    {
        $stockRepository = app(StockRepository::class);
        $stockQueryBuilder = $stockRepository->model;

        $queryBuilder = $this->model;

        if (isset($searchCriteria['endDate'])) {
            $stockQueryBuilder = $stockQueryBuilder->whereDate('expiredDate', '<=', Carbon::parse($searchCriteria['endDate']));

            $queryBuilder = $queryBuilder->hasStockExpiration($searchCriteria['endDate']);

            unset($searchCriteria['endDate']);
        }

        unset($searchCriteria['expiredStartDate']);
        unset($searchCriteria['expiredEndDate']);

        if (isset($searchCriteria['startDate'])) {
            $stockQueryBuilder = $stockQueryBuilder->whereDate('expiredDate', '>=', Carbon::parse($searchCriteria['startDate']));
            unset($searchCriteria['startDate']);
        }

        if (isset($searchCriteria['branchId'])) {
            $stockQueryBuilder = $stockQueryBuilder->where('branchId', $searchCriteria['branchId']);
            unset($searchCriteria['branchId']);
        }
        if (isset($searchCriteria['quantity'])) {
            unset($searchCriteria['quantity']);
        }
        if (isset($searchCriteria['havingStockAlertQuantity'])) {
            unset($searchCriteria['havingStockAlertQuantity']);
        }

        $searchCriteria['id'] = $stockQueryBuilder->pluck('productId')->toArray();

        $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $queryBuilder->orderBy($orderBy, $orderDirection);

        if (empty($searchCriteria['withoutPagination'])) {
            return $queryBuilder->paginate($limit);
        } else {
            return $queryBuilder->get();
        }
    }

    /**
     * @inheritDoc
     */
    public function getProductGroupByStock(array $searchCriteria = [])
    {
        $thisModelTable = $this->model->getTable();
        $stockModelTable = Stock::getTableName();
        $discountModelTable = Discount::getTableName();
        $taxModelTable = Tax::getTableName();
        $unitModelTable = Unit::getTableName();
        $categoryModelTable = Category::getTableName();
        $branchModelTable = Branch::getTableName();
        $companyModelTable = Company::getTableName();

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;

        $queryBuilder = DB::table($thisModelTable)
            ->select($thisModelTable . ".*", $stockModelTable . '.id as stockId', $stockModelTable . '.sku', $stockModelTable . '.branchId', $stockModelTable . '.quantity', $stockModelTable . '.unitPrice', $stockModelTable . '.unitCost',
                $discountModelTable . '.type as discountType', $discountModelTable . '.amount as discountAmount', $discountModelTable . '.endDate as discountEndDate',
                $taxModelTable . '.type as taxType', $taxModelTable . '.amount as taxAmount', $taxModelTable . '.action as taxAction',
                $unitModelTable . '.name as unitName', $unitModelTable . '.isFraction', $categoryModelTable . '.name as categoryName', $branchModelTable . '.name as branchName',
                $companyModelTable . '.name as companyName')
            ->join($stockModelTable, $thisModelTable . '.id', '=', $stockModelTable . '.productId')
            ->leftJoin($discountModelTable, function ($join) use ($discountModelTable, $thisModelTable) {
                $join->on($discountModelTable . '.id', '=', $thisModelTable . '.discountId');
            })
            ->leftJoin($taxModelTable, function ($join) use ($taxModelTable, $thisModelTable) {
                $join->on($taxModelTable . '.id', '=', $thisModelTable . '.taxId');
            })
            ->leftJoin($unitModelTable, function ($join) use ($unitModelTable, $thisModelTable) {
                $join->on($unitModelTable . '.id', '=', $thisModelTable . '.unitId');
            })
            ->leftJoin($categoryModelTable, function ($join) use ($categoryModelTable, $thisModelTable) {
                $join->on($categoryModelTable . '.id', '=', $thisModelTable . '.categoryId');
            })
            ->leftJoin($branchModelTable, function ($join) use ($branchModelTable, $stockModelTable) {
                $join->on($branchModelTable . '.id', '=', $stockModelTable . '.branchId');
            })
            ->leftJoin($companyModelTable, function ($join) use ($companyModelTable, $thisModelTable) {
                $join->on($thisModelTable . '.companyId', '=', $companyModelTable . '.id');
            })
            ->where([
                [$thisModelTable . '.deleted_at', '=', null],
                [$stockModelTable . '.quantity', '>', 0]
            ])
            ->whereNull($stockModelTable . '.deleted_at')
            ->groupBy('sku');


        if (isset($searchCriteria['query'])) {
            $queryBuilder = $queryBuilder->where(function ($query) use ($thisModelTable, $searchCriteria) {
                $query->where($thisModelTable . '.name', 'like', '%' . $searchCriteria['query'] . '%')
                    ->orWhere($thisModelTable . '.barcode', 'like', '%' . $searchCriteria['query'] . '%');
            });
        }

        if (isset($searchCriteria['havingStockAlertQuantity'])) {
            $queryBuilder = $queryBuilder->where($thisModelTable . '.alertQuantity', '>=', $stockModelTable . '.quantity');
        }

        if (isset($searchCriteria['branchId'])) {
            $queryBuilder = $queryBuilder->where($stockModelTable . '.branchId', $searchCriteria['branchId']);
        }

        if (empty($searchCriteria['withoutPagination'])) {
            return $queryBuilder->paginate($limit);
        } else {
            return $queryBuilder->get();
        }

    }

    /**
     * inherit doc
     */
    public function save(array $data): \ArrayAccess
    {
        DB::beginTransaction();

        $barcodeType = '';
        if (isset($data['barcodeType'])) {
            $barcodeType = $data['barcodeType'];
            unset($data['barcodeType']);
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

        $product = parent::save($data);

        if (!empty($data['bundle'])) {
            $bundle = Bundle::create($data['bundle']);

            $product->update(['bundleId' => $bundle->id]);

            $offerPromoterProducts = $data['bundle']['offerPromoterProducts'];
            if (isset($offerPromoterProducts))
                collect($offerPromoterProducts)->each(function($item, $key) use($bundle) {
                    OfferPromoterProduct::create([
                        'quantity' => $item['quantity'],
                        'productId' => $item['productId'],
                        'bundleId' => $bundle->id,
                    ]);
                });

            if (isset($data['bundle']['offerProducts'])){
                $offerProducts = $data['bundle']['offerProducts'];
                collect($offerProducts)->each(function($item, $key) use($bundle) {
                    $offerProductData = [
                        'quantity' => $item['quantity'],
                        'productId' => $item['productId'],
                        'bundleId' => $bundle->id,
                    ];
                    if (isset($item['discountType'])){
                        $offerProductData['discountType'] = $item['discountType'];
                    }
                    if (isset($item['discountAmount'])){
                        $offerProductData['discountAmount'] = $item['discountAmount'];
                    }
                    OfferProduct::create($offerProductData);
                });
            }

            unset($data['bundle']);
        }

        DB::commit();

        if (isset($data['variations'])) {
            event(new ProductVariantsCreatedEvent($product, $data['variations']));
        }

        if (isset($data['openingStock'])) {
            event(new ProductOpeningStockCreatedEvent($product, $data['openingStock']));
        }

        event(new ProductCreatedEvent($product, $barcodeType));

        //related to woocommerce
        $branch = Branch::where('type', Branch::TYPE_ECOMMERCE)->first();
        if ($branch instanceof Branch) {
            event(new ProductSavingEvent('saved', $product, $branch));
        }

        return $product;
    }

    public function update(\ArrayAccess $model, array $data, $addRequest = true): \ArrayAccess
    {
        DB::beginTransaction();

        $barcodeType = '';
        if (isset($data['barcodeType'])) {
            $barcodeType = $data['barcodeType'];
            unset($data['barcodeType']);
        }

        //Ref: on demand by ajgar bhai
        if (isset($data['isDiscountApplicable']) && $data['isDiscountApplicable'] === false) {
            $data['discountId'] = null;
        }

        $product = parent::update($model, $data);

        DB::commit();

        if (isset($data['variations'])) {
            event(new ProductVariantsCreatedEvent($product, $data['variations']));
        }

        event(new ProductUpdatedEvent($product, $barcodeType, $this->generateEventOptionsForModel([], $addRequest)));

        //related to woocomm
        $branch = Branch::where('type', Branch::TYPE_ECOMMERCE)->first();
        if ($branch instanceof Branch && $product->wcProductId >= 0) {
            event(new ProductSavingEvent('updated', $product, $branch));
        }

        return $product;
    }

    /**
     * shorten the search based on search criteria
     *
     * @param array $searchCriteria
     * @return mixed
     */
    public function applyFilterInProductSearch(array $searchCriteria = [], bool $onlyTrashed = false)
    {
        if (isset($searchCriteria['query'])) {
            $model = $onlyTrashed ? $this->model->onlyTrashed() : $this->model;
            $searchCriteria['id'] = $model->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('barcode', 'like', $searchCriteria['query'] . '%')
                ->orWhereHas('category', function ($query) use ($searchCriteria) {
                    $query->where('name', 'like', $searchCriteria['query'] . '%');
                })
                ->orWhereHas($onlyTrashed ? 'archiveStocks' : 'stocks', function ($query) use ($searchCriteria) {
                    $query->where('sku', 'like', $searchCriteria['query'] . '%');
                })
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }

        if (isset($searchCriteria['sku'])) {
            $stockRepository = app(StockRepository::class);
            $stockRepositoryModel = $onlyTrashed ? $stockRepository->model->onlyTrashed() : $stockRepository->model;
            $productIds = $stockRepositoryModel->where('sku', $searchCriteria['sku'])->pluck('productId')->toArray();

            $searchCriteria['id'] = isset($searchCriteria['id']) ? array_intersect($searchCriteria['id'], $productIds) : $productIds;

            unset($searchCriteria['sku']);
        }

        if (isset($searchCriteria['id'])) {
            $searchCriteria['id'] = is_array($searchCriteria['id']) ? implode(",", array_unique($searchCriteria['id'])) : $searchCriteria['id'];
        }

        return $searchCriteria;
    }

    public function getArchivedProductAndProductWithArchiveStocks(array $searchCriteria = [])
    {

        $queryBuilder = $this->model;

        if (isset($searchCriteria['isStocksArchived'])) {
            $queryBuilder = $queryBuilder->withTrashed();
        } else {
            $queryBuilder = $queryBuilder->onlyTrashed();
        }

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;


        //Only archived stocks
        if (isset($searchCriteria['isStocksArchived'])) {
            // Date range filter by archived stocks deleted_at timestamp
            if (isset($searchCriteria['endDate'])) {
                $queryBuilder->whereHas('archiveStocks', function ($query) use ($searchCriteria) {
                    $query->whereDate('deleted_at', '<=', Carbon::parse($searchCriteria['endDate']));
                });
                unset($searchCriteria['endDate']);
            }
            if (isset($searchCriteria['startDate'])) {
                $queryBuilder->whereHas('archiveStocks', function ($query) use ($searchCriteria) {
                    $query->whereDate('deleted_at', '>=', Carbon::parse($searchCriteria['startDate']));
                });
                unset($searchCriteria['startDate']);
            }

            // Fetch the only archived Stocks
            if (empty($searchCriteria['endDate']) && empty($searchCriteria['startDate']) && empty($searchCriteria['query'])) {
                $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
                    $query->whereHas('archiveStocks', function ($query) use ($searchCriteria) {
                        $query->where('deleted_at', '!=', null);
                    });
                });
                unset($searchCriteria['isStocksArchived']);
            }

            // Archive stocks filter query
            if (isset($searchCriteria['query'])) {
                $queryBuilder = $queryBuilder->whereHas('archiveStocks', function ($query) use ($searchCriteria) {
                    $query->where('sku', 'like', $searchCriteria['query'] . '%');
                });
                unset($searchCriteria['query']);
            }

        }

        // Date range filter by archived product deleted_at timestamp
        if (empty($searchCriteria['isStocksArchived'])) {

            if (isset($searchCriteria['endDate'])) {
                $queryBuilder->whereDate('deleted_at', '<=', Carbon::parse($searchCriteria['endDate']));
                unset($searchCriteria['endDate']);
            }

            if (isset($searchCriteria['startDate'])) {
                $queryBuilder->whereDate('deleted_at', '>=', Carbon::parse($searchCriteria['startDate']));
                unset($searchCriteria['startDate']);
            }
        }

        // filter by query inside archive product
        if (isset($searchCriteria['query'])) {
            $queryBuilder = $queryBuilder->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('barcode', 'like', $searchCriteria['query'] . '%')
                ->orWhereHas('category', function ($query) use ($searchCriteria) {
                    $query->where('name', 'like', $searchCriteria['query'] . '%');
                });

            unset($searchCriteria['query']);
        }

        if (isset($searchCriteria['id'])) {
            $queryBuilder = $queryBuilder->where('id', '=', $searchCriteria['id']);
            unset($searchCriteria['id']);
        }

        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $queryBuilder->orderBy($orderBy, $orderDirection);

        if (empty($searchCriteria['withoutPagination'])) {
            return $queryBuilder->paginate($limit);
        } else {
            return $queryBuilder->get();
        }

    }

    public function delete(\ArrayAccess $model): bool
    {
        if (count($model->stocks) > 0) {
            $totalQuantity = $model->stocks->sum('quantity');
            if ($totalQuantity > 0) {
                throw ValidationException::withMessages([
                    'archiveProduct' => ["Product can't be archived because the product has stock in another branch!"]
                ]);
            }
        }
        $user = Auth::user();
        $model->archivedByUserId = $user->id;
        $model->save();
        return parent::delete($model);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function restore($id)
    {
        $product = $this->model->withTrashed()->find($id);

        $product->restore();

        return $product;
    }
}
