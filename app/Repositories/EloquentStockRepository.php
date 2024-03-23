<?php


namespace App\Repositories;


use App\Events\Stock\StockCreatedEvent;
use App\Events\Stock\StockMovedEvent;
use App\Events\Stock\StockUpdatedEvent;
use App\Events\Woocommerce\StockSavingEvent;
use App\Models\Branch;
use App\Models\OfferProduct;
use App\Models\OfferPromoterProduct;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Stock;
use App\Models\StockLog;
use App\Repositories\Contracts\StockLogRepository;
use App\Repositories\Contracts\StockRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EloquentStockRepository extends EloquentBaseRepository implements StockRepository
{
    /**
     * @inheritdoc
     * @throws ValidationException
     */
    public function save(array $data): \ArrayAccess
    {
        DB::beginTransaction();

        if (empty($data['sku']) && isset($data['productId'])){
            $data['sku'] = $data['sku'] ?? Purchase::generateSku("bundle-product", $data['productId'], $data['unitPrice']);
        }
        if (isset($data['bundleOfferPromoterProducts'])){
            $bundleOfferPromoterProducts = $data['bundleOfferPromoterProducts'];
            $this->updateBundleProductStock($bundleOfferPromoterProducts);

            $product = Product::query()
                ->where('id', $data['productId'])
                ->first(['id', 'bundleId']);

            foreach ($bundleOfferPromoterProducts as $bundleOfferPromoterProduct){
                OfferPromoterProduct::query()
                    ->where('bundleId', $product->bundleId)
                    ->where('productId', $bundleOfferPromoterProduct['productId'])
                    ->update(['stockId' => $bundleOfferPromoterProduct['stockId']]);
            }
        }
        if (isset($data['bundleOfferProducts'])){
            $bundleOfferProducts = $data['bundleOfferProducts'];
            $this->updateBundleProductStock($bundleOfferProducts);

            $product = Product::query()
                ->where('id', $data['productId'])
                ->first(['id', 'bundleId']);

            foreach ($bundleOfferProducts as $bundleOfferProduct){
                OfferProduct::query()
                    ->where('bundleId', $product->bundleId)
                    ->where('productId', $bundleOfferProduct['productId'])
                    ->update(['stockId' => $bundleOfferProduct['stockId']]);
            }
        }

        $stock = parent::save($data);

//        event(new StockCreatedEvent($stock));

        $stockLogRepository = app(StockLogRepository::class);
        $stockLogRepository->save([
            'stockId' => $stock->id,
            'productId' => $stock->productId,
            'resourceId' => $stock->id,
            'type' => StockLog::TYPE_RESTOCK_FROM_BUNDLE_PRODUCT,
            'prevQuantity' => 0,
            'newQuantity' => $stock->quantity,
            'quantity' => $stock->quantity,
            'prevUnitCost' => $stock->unitCost,
            'newUnitCost' => $stock->unitCost,
            'prevUnitPrice' => $stock->unitPrice,
            'newUnitPrice' => $stock->unitPrice,
            'prevExpiredDate' => $stock->expiredDate,
            'newExpiredDate' => $stock->expiredDate,
            'date' => Carbon::now(),
        ]);

        if($stock->branch->type == Branch::TYPE_ECOMMERCE) {
            event(new StockSavingEvent('saved', $stock, $stock->product->wcProductId));
        }

        DB::commit();

        return $stock;
    }

    /**
     * @inheritdoc
     */
    public function update(\ArrayAccess $model, array $data): \ArrayAccess
    {
        $stock = parent::update($model, $data);

        event(new StockUpdatedEvent($stock, $this->generateEventOptionsForModel()));

        if($stock->branch->type == Branch::TYPE_ECOMMERCE && $stock->wcStockId) {
            event(new StockSavingEvent('updated', $stock, $stock->product->wcProductId));
        }

        return $stock;
    }

    /**
     * @param \ArrayAccess $model
     * @param array $data
     * @return \ArrayAccess
     * @throws ValidationException
     */
    public function updateCombProduct(\ArrayAccess $model, array $data): \ArrayAccess
    {
        DB::beginTransaction();

        $this->adjustOldStock($model, $data);

        if (isset($data['bundleOfferPromoterProducts'])) {
            $bundleOfferPromoterProducts = $data['bundleOfferPromoterProducts'];
            $this->updateBundleProductStock($bundleOfferPromoterProducts);
            unset($data['bundleOfferPromoterProducts']);
        }

        if (isset($data['bundleOfferProducts'])){
            $bundleOfferProducts = $data['bundleOfferProducts'];
            $this->updateBundleProductStock($bundleOfferProducts);
            unset($data['bundleOfferProducts']);
        }

        $stock = parent::update($model, $data);

        event(new StockUpdatedEvent($stock, $this->generateEventOptionsForModel()));

        if($stock->branch->type == Branch::TYPE_ECOMMERCE && $stock->wcStockId) {
            event(new StockSavingEvent('updated', $stock, $stock->product->wcProductId));
        }

        DB::commit();

        return $stock;
    }

    /**
     * @param $stock
     * @param $data
     * @return void
     */
    private function adjustOldStock($stock, $data)
    {
        $bundleStock = $stock;

        $product = Product::query()
            ->where('id', $data['productId'])
            ->first(['id', 'bundleId']);

        $offerProducts = OfferProduct::query()
            ->where('bundleId', $product->bundleId)
            ->get();

        if ($offerProducts->count()){
            $offerProducts->each(function($item, $key) use ($bundleStock) {
                $stockRepository = app(StockRepository::class);
                $stock = $stockRepository->findOne($item['stockId']);

                if ($stock instanceof Stock){
                    $oldStock = clone($stock);

                    $data = [
                        'quantity' => $stock->quantity + ($item['quantity'] * $bundleStock->quantity)
                    ];

                    $newStock = $stockRepository->update($stock, $data);
                    event(new StockMovedEvent($newStock, $oldStock));
                }
            });
        }

        $offerPromoterProducts = OfferPromoterProduct::query()
            ->where('bundleId', $product->bundleId)
            ->get();

        if ($offerPromoterProducts->count()){
            $offerPromoterProducts
                ->each(function($item, $key) use ($bundleStock) {
                    $stockRepository = app(StockRepository::class);
                    $stock = $stockRepository->findOne($item['stockId']);

                    if ($stock instanceof Stock){
                        $oldStock = clone($stock);

                        $data = [
                            'quantity' => $stock->quantity + ($item['quantity'] * $bundleStock->quantity)
                        ];

                        $newStock = $stockRepository->update($stock, $data);
                        event(new StockMovedEvent($newStock, $oldStock));
                    }
                });
        }
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false, $onlyTrashed = false)
    {
        $queryBuilder = $this->model;

        if (isset($searchCriteria['endDate'])) {
            $queryBuilder = $queryBuilder->whereDate('created_at', '<=', Carbon::parse($searchCriteria['endDate']));
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder = $queryBuilder->whereDate('created_at', '>=', Carbon::parse($searchCriteria['startDate']));
            unset($searchCriteria['startDate']);
        }

        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('sku', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhereHas('product', function ($query) use ($searchCriteria) {
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                    ->orWhere('barcode', 'like', '%' . $searchCriteria['query'] . '%');
                })
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }

        if (isset($searchCriteria['outOfStock'])) {
            $queryBuilder = $queryBuilder->where('quantity', '<=', 0);
            unset($searchCriteria['outOfStock']);
        }

        $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $queryBuilder->orderBy($orderBy, $orderDirection);

        if ($withTrashed) {
            $queryBuilder->withTrashed();
        }
        if ($onlyTrashed){
            $queryBuilder->onlyTrashed();
        }


        if (empty($searchCriteria['withoutPagination'])) {
            return $queryBuilder->paginate($limit);
        } else {
            return $queryBuilder->get();
        }
    }

    public function getProductGroupByStock(array $searchCriteria = [])
    {
        $queryBuilder = $this->model;

        $exactBarcodeSearch = false;
        if (isset($searchCriteria['isExactBarcodeSearch'])) {
            $exactBarcodeSearch = true;
            unset($searchCriteria['isExactBarcodeSearch']);
        }

        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('sku', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhereHas('product', function ($query) use ($searchCriteria, $exactBarcodeSearch) {
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%');
                    $query->when($exactBarcodeSearch, function ($q) use ($searchCriteria) {
                        $q->orWhere('barcode', $searchCriteria['query']);
                    });
                    $query->when(!$exactBarcodeSearch, function ($q) use ($searchCriteria) {
                        $q->orWhere('barcode', 'like', '%' . $searchCriteria['query'] . '%');
                    });
                })->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }

        if (isset($searchCriteria['havingStockAlertQuantity'])) {
            $queryBuilder = $queryBuilder->whereHas('product', function ($query) use ($searchCriteria) {
                $query->whereColumn('stocks.quantity', '<=', 'products.alertQuantity');
            });
            unset($searchCriteria['havingStockAlertQuantity']);
        }

        $groupByMostSale = isset($searchCriteria['isGroupByMostSale']);
        unset($searchCriteria['isGroupByMostSale']);

        $queryBuilder = $queryBuilder->where('quantity', '>', 0);

        $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';

        if($groupByMostSale) {
            $queryBuilder = $queryBuilder->withCount(['orderProducts as totalSaleQuantity' => function($query) {
                $query->select(DB::raw('COALESCE(sum(quantity),0) as totalSaleQuantity'));
            }]);

            $queryBuilder = $queryBuilder->orderBy('totalSaleQuantity', 'desc');
        } else {
            $queryBuilder->orderBy($orderBy, $orderDirection);
        }

        $queryBuilder->groupBy('sku', 'expiredDate');

        if (empty($searchCriteria['withoutPagination'])) {
            return $queryBuilder->paginate($limit);
        } else {
            return $queryBuilder->get();
        }
    }

    /**
     * @throws ValidationException
     */
    public function delete(\ArrayAccess $model): bool
    {
        if ($model['quantity'] <= 0) {
            $user= Auth::user();
            $model->archivedByUserId = $user->id;
            $model->save();
            return parent::delete($model); // TODO: Change the autogenerated stub
        } else {
            throw ValidationException::withMessages([
                'stock' => ["You can't archive stock when stock has quantity."]
            ]);
        }
    }

    /**
     * @return JsonResponse
     */
    public function mergeStock(): JsonResponse
    {
        $stocks = $this->model
            ->select('*' ,DB::raw("SUM(`quantity`) as newQuantity"))
            ->groupBy('branchId', 'productId', 'sku', 'unitCost', 'unitPrice', 'expiredDate', 'productVariationId')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $stockLogRepository = app(StockLogRepository::class);

        DB::beginTransaction();

        foreach ($stocks as $stock) {
            $this->model->where('id', $stock->id)->update(['quantity' => $stock->newQuantity]);

            $mergeQuantity =  $stock->newQuantity - $stock->quantity;

            $stockLogRepository->save([
                'stockId' => $stock->id,
                'productId' => $stock->productId,
                'resourceId' => $stock->id,
                'type' => StockLog::TYPE_STOCK_MERGE_UPDATE,
                'prevQuantity' => $stock->quantity,
                'newQuantity' => $mergeQuantity,
                'quantity' => $stock->newQuantity,
                'date' => Carbon::now(),
            ]);

            $deleteStockQuery = $this->model->where('branchId', $stock->branchId)
                ->where('productId', $stock->productId)
                ->where('sku', $stock->sku)
                ->whereNotIn('id', [$stock->id]);

            $deleteStocks = $deleteStockQuery->get();

            $deleteStockQuery->update(['quantity' => 0]);

            foreach ($deleteStocks as $deleteStock) {
                $stockLogRepository->save([
                    'stockId' => $deleteStock->id,
                    'productId' => $deleteStock->productId,
                    'resourceId' => $deleteStock->id,
                    'type' => StockLog::TYPE_STOCK_MERGE_DELETE,
                    'prevQuantity' => $deleteStock->quantity,
                    'newQuantity' => $deleteStock->quantity,
                    'quantity' => 0,
                    'date' => Carbon::now(),
                ]);
            }

            $deleteStockQuery->delete();
        }

        DB::commit();

        return response()->json(['message' => "Stock Data merged successfully"]);
    }
    /**
     * @throws ValidationException
     */
    private function updateBundleProductStock($products)
    {
        collect($products)->each(function($item, $key) {
            $stockRepository = app(StockRepository::class);
            $stock = $stockRepository->findOne($item['stockId']);

            if ($stock instanceof Stock){
                $oldStock = clone($stock);
                $stockQuantity = $stock->quantity;
                if ($stockQuantity < $item['freezQuantity'] || ($stockQuantity - $item['freezQuantity']) < 0){
                    throw ValidationException::withMessages(['message' => "Product stock is not available"]);
                }
                $data = [
                    'quantity' => $stock->quantity - $item['freezQuantity']
                ];

                $newStock = $stockRepository->update($stock, $data);
                event(new StockMovedEvent($newStock, $oldStock));
            }else{
                throw ValidationException::withMessages(['message' => "Stock not found"]);
            }
        });
    }

}
