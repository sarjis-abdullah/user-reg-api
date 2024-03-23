<?php


namespace App\Repositories;

use App\Events\StockTransfer\StockTransferCreatedEvent;
use App\Events\StockTransfer\StockTransferUpdatedEvent;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Stock;
use App\Models\StockLog;
use App\Models\StockTransfer;
use App\Models\StockTransferProduct;
use App\Models\Supplier;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\DeliveryRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\PurchaseRepository;
use App\Repositories\Contracts\StockLogRepository;
use App\Repositories\Contracts\StockRepository;
use App\Repositories\Contracts\StockTransferProductRepository;
use App\Repositories\Contracts\StockTransferRepository;
use App\Repositories\Contracts\SupplierRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class EloquentStockTransferRepository extends EloquentBaseRepository implements StockTransferRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        Cache::flush();
        $queryBuilder = $this->model->newQuery();
        $stockTransferProductModel = new StockTransferProduct();

        if (isset($searchCriteria['endDate'])) {
            $queryBuilder = $queryBuilder->whereDate('created_at', '<=', Carbon::parse($searchCriteria['endDate'])->toDateString());
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder = $queryBuilder->whereDate('created_at', '>=', Carbon::parse($searchCriteria['startDate'])->toDateString());
            unset($searchCriteria['startDate']);
        }

        if (isset($searchCriteria['branchId'])) {
            $queryBuilder = $queryBuilder->where('fromBranchId', $searchCriteria['branchId'])->orWhere('toBranchId', $searchCriteria['branchId']);
            unset($searchCriteria['branchId']);
        }
        if (isset($searchCriteria['statusList'])) {
            $statuses = explode(',', $searchCriteria['statusList']);
            $queryBuilder = $queryBuilder->whereIn('status', $statuses);
            unset($searchCriteria['statusList']);
        }

        if (isset($searchCriteria['referenceNumber'])) {
            $queryBuilder = $queryBuilder->where('referenceNumber', $searchCriteria['referenceNumber'])->orWhere('referenceNumber', $searchCriteria['referenceNumber']);
            unset($searchCriteria['referenceNumber']);
        }

        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('referenceNumber', 'like', '%' . $searchCriteria['query'] . '%')
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }

        $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $queryBuilder->orderBy($orderBy, $orderDirection);

        /*
         * Summary Calculation Start Here
         * */
        $stockTransferIds = $queryBuilder->pluck('id')->toArray();

        $stockTransferProducts = $stockTransferProductModel->newQuery()
            ->whereIn('stockTransferId', $stockTransferIds)
            ->select(
                DB::raw('SUM(stock_transfer_products.totalAmount) as totalCostPrice'),
                DB::raw('SUM(stocks.unitPrice * stock_transfer_products.quantity) as totalSellPrice'),
            )
            ->leftJoin('stocks', function ($join) {
                $join->on('stocks.sku', '=', 'stock_transfer_products.sku')
                    ->on('stocks.productId', '=', 'stock_transfer_products.productId')
                    ->on('stocks.branchId', '=', 'stock_transfer_products.fromBranchId');
            })
            ->first();

        $summary = [
          'totalCostPrice' => round($stockTransferProducts->totalCostPrice, 2),
          'totalSellPrice' => round($stockTransferProducts->totalSellPrice, 2),
        ];

        /*
         * Summary Calculation End Here
         * */

        /*
         * Page Wise Summary Calculation Start Here
         * */

        $page = !empty($searchCriteria['page']) ? (int)$searchCriteria['page'] : 1;
        $paginationData = $queryBuilder->paginate($limit, ['*'], 'page', $page);

        $paginateStockTransferProducts = $stockTransferProductModel->newQuery()
            ->whereIn('stockTransferId', $paginationData->pluck('id')->toArray())
            ->select(
                DB::raw('SUM(stock_transfer_products.totalAmount) as totalCostPrice'),
                DB::raw('SUM(stocks.unitPrice * stock_transfer_products.quantity) as totalSellPrice'),
            )
            ->leftJoin('stocks', function ($join) {
                $join->on('stocks.sku', '=', 'stock_transfer_products.sku')
                    ->on('stocks.productId', '=', 'stock_transfer_products.productId')
                    ->on('stocks.branchId', '=', 'stock_transfer_products.fromBranchId');
            })
            ->first();

        $pageWiseSummary = [
            'totalCostPrice' => round($paginateStockTransferProducts->totalCostPrice, 2),
            'totalSellPrice' => round($paginateStockTransferProducts->totalSellPrice, 2),
        ];

        /*
         * Page Wise Summary Calculation End Here
         * */

        if (empty($searchCriteria['withoutPagination'])) {
            $result = $paginationData;
        } else {
            $result = $queryBuilder->get();
        }

        return ['result' => $result, 'summary' => $summary, 'pageWiseSummary' => $pageWiseSummary];
    }

    /**
     * @inheritDoc
     */
    public function save(array $data): \ArrayAccess
    {
        DB::beginTransaction();

        // save into stock_transfers table
        $data['referenceNumber'] = 'ST-' . Carbon::now()->format('YmdHis');
        $data['status'] = $data['status'] ?? StockTransfer::STATUS_PENDING;

        if (isset($data['deliveryAgencyId']) || isset($data['deliveryPersonId'])) {
            $delivery = $this->saveAndGetDelivery($data);
            $data['deliveryId'] = $delivery->id;
        }

        $stockTransfer = parent::save($data);

        // save into stock_transfer_products table
        $stockTransferProductRepository = app(StockTransferProductRepository::class);
        foreach ($data['products'] as $product) {
            $stockTransferProductData = [];
            $stockTransferProductData['stockTransferId'] = $stockTransfer->id;
            $stockTransferProductData['fromBranchId'] = $data['fromBranchId'];
            $stockTransferProductData['toBranchId'] = $data['toBranchId'];
            $stockTransferProductData['productId'] = $product['productId'];
            $stockTransferProductData['quantity'] = $product['quantity'];
            $stockTransferProductData['sku'] = $product['sku'];
            $stockTransferProductData['unitCostToBranch'] = $product['unitCostToBranch'] ?? null;
            $stockTransferProductData['totalAmount'] = $product['totalAmount'] ?? null;

            $stockTransferProduct = $stockTransferProductRepository->save($stockTransferProductData);

            // update branch stock
            $this->updateFromBranchStock($stockTransferProduct);
        }

        DB::commit();

        return $stockTransfer;
    }

    /**
     * @inheritDoc
     */
    public function updateStockTransfer(StockTransfer $stockTransfer, array $data): \ArrayAccess
    {
        if (!empty($data['status']) && in_array($stockTransfer->status, [StockTransfer::STATUS_CANCELLED, StockTransfer::STATUS_DECLINED, StockTransfer::STATUS_RECEIVED])) {
            throw ValidationException::withMessages(['status' => 'Can\'t update stock transfer status that is already cancelled, declined or received.']);
        }

        DB::beginTransaction();

        if (!empty($data['status']) && $data['status'] == StockTransfer::STATUS_RECEIVED) {
            $fromBranch = $stockTransfer->fromBranch;
            $toBranch = $stockTransfer->toBranch;

            if(in_array($fromBranch->type, [Branch::TYPE_WAREHOUSE, Branch::TYPE_SELF]) && $toBranch->type == Branch::TYPE_FRANCHISE) {
                //add transfer as purchase for franchise
                $purchaseRepository = app(PurchaseRepository::class);
                $stockRepository = app(StockRepository::class);

                $supplier = app(SupplierRepository::class)->patch(
                    ['name' => $fromBranch->name, 'type' => Supplier::TYPE_REGULAR],
                    ['name' => $fromBranch->name, 'type' => Supplier::TYPE_REGULAR]
                );

                $purchaseProductsData = $stockTransfer->stockTransferProducts->map(function ($stProduct) use ($stockRepository) {
                    $fromBranchStock = $stockRepository->findOneBy(['productId' => $stProduct->productId, 'sku' => $stProduct->sku, 'branchId' => $stProduct->fromBranchId], true); // get thrashed stock if from stock deleted for any reason

                    $unitCost = $stProduct->unitCostToBranch;

                    $sku = $fromBranchStock->sku . '-' . $stProduct->id;
                    return [
                        "productId" => $stProduct->productId,
                        "productVariationId" => $stProduct->productVariationId,
                        "sku" => $sku,
                        "stockId" => $fromBranchStock->id,
                        "quantity" => (float) $stProduct->quantity,
                        "actualUnitCost" => (float) $fromBranchStock->unitCost,
                        "unitCost" => $unitCost,
                        "sellingPrice" => (float) $fromBranchStock->unitPrice,
                        "taxAmount" => 0,
                        "totalAmount" => (float) $stProduct->quantity * $unitCost,
                        "expiredDate" => $fromBranchStock->expiredDate,
                        "discountType" => "flat",
                        "discountAmount" => 0,
                    ];
                });

                $purchaseData = [
                    "createdByUserId" => $this->getLoggedInUser()->id,
                    "supplierId" => $supplier->id,
                    "branchId" => $toBranch->id,
                    "status" => "received",
                    "totalAmount" => $purchaseProductsData->sum('totalAmount'),
                    "discountAmount" => 0,
                    "shippingCost" => $stockTransfer->shippingCost ?? 0,
                    "taxAmount" => 0,
                    "note" => sprintf('This purchase is made from %s', $supplier->name),
                    "due" => $purchaseProductsData->sum('totalAmount'),
                    "paid" => 0,
                    "date" => Carbon::now(),
                    "purchaseProducts" => $purchaseProductsData
                ];

                $purchaseRepository->save($purchaseData);

                //add transfer as order for branch or warehouse
                $orderRepository = app(OrderRepository::class);

                $customer = app(CustomerRepository::class)->patch(
                    ['name' => $toBranch->name, 'phone' => $toBranch->phone],
                    ['name' => $toBranch->name, 'phone' => $toBranch->phone, 'type' => Customer::TYPE_REGULAR, 'status' => Customer::STATUS_ACTIVE]
                );

                $orderProductsData = $purchaseProductsData->map(function ($pProduct) {
                   return [
                        "productId" => $pProduct['productId'],
                        "stockId" => $pProduct['stockId'],
                        "unitPrice" => $pProduct['sellingPrice'],
                        "discountedUnitPrice" => $pProduct['unitCost'],
                        "quantity" => $pProduct['quantity'],
                        "amount" => $pProduct['totalAmount'],
                        "profitAmount" => $pProduct['quantity'] * ($pProduct['unitCost'] - $pProduct['actualUnitCost']),
                        "discountId" => null,
                        "discount" => 0,
                        "taxId" => null,
                        "tax" => 0
                   ];
                });

                $orderData = [
                    "createdByUserId" => $stockTransfer->createdByUserId,
                    "branchId" => $fromBranch->id,
                    "customerId" => $customer->id,
                    "amount" => $orderProductsData->sum('amount'),
                    "roundOffAmount" => 0,
                    "discount" => 0,
                    "tax" => 0,
                    "shippingCost" => $stockTransfer->shippingCost ?? 0,
                    "due" => $orderProductsData->sum('amount'),
                    "deliveryMethod" => Order::DELIVERY_METHOD_TRANSFER,
                    "date" => Carbon::now(),
                    "comment" => sprintf("The order is made by stock transfer to %s", $toBranch->name),
                    "orderProducts" => $orderProductsData,
                ];

                $orderRepository->save($orderData);

                $stockTransferProducts = $stockTransfer->stockTransferProducts;
                foreach ($stockTransferProducts as $stockTransferProduct) {
                    $this->updateFromBranchStock($stockTransferProduct, true);
                }
            } else {
                $stockTransferProducts = $stockTransfer->stockTransferProducts;
                foreach ($stockTransferProducts as $stockTransferProduct) {
                    $this->updateToBranchStock($stockTransferProduct);
                }
            }
        }

        if (!empty($data['status']) && in_array($data['status'], [StockTransfer::STATUS_CANCELLED, StockTransfer::STATUS_DECLINED])) {
            $stockTransferProducts = $stockTransfer->stockTransferProducts;
            foreach ($stockTransferProducts as $stockTransferProduct) {
                $this->updateFromBranchStock($stockTransferProduct, true);
            }
        }

        // update delivery info
        if (!empty($data['deliveryAgencyId'])
            || !empty($data['deliveryPersonName'])
            || !empty($data['deliveryPersonId'])
            || !empty($data['deliveryTrackingNumber'])
            || !empty($data['fromDeliveryPhone'])) {

            if ($stockTransfer->delivery){
                $delivery = $stockTransfer->delivery;
            }else{
                $delivery = $this->saveAndGetDelivery($data);
                $data['deliveryId'] = $delivery->id;
            }

            $deliveryRepository = app(DeliveryRepository::class);
            $deliveryData = [];

            $deliveryData['deliveryAgencyId'] = $data['deliveryAgencyId'] ?? null;
            $deliveryData['deliveryPersonId'] = $data['deliveryPersonId'] ?? null;
            $deliveryData['deliveryPersonName'] = $data['deliveryPersonName'] ?? null;
            $deliveryData['note'] = $data['sendingNote'] ?? $data['receivedNote'] ?? $delivery->note;
            $deliveryData['trackingNumber'] = $data['deliveryTrackingNumber'] ?? null;
            $deliveryData['fromDeliveryPhone'] = $data['fromDeliveryPhone'] ?? null;
            $deliveryData['status'] = $data['status'] ?? $delivery->status;

            $deliveryRepository->update($delivery, $deliveryData);
        }

        $stockTransfer =  parent::update($stockTransfer, $data);

        DB::commit();

        if(!empty($data['status']) && in_array($data['status'], [StockTransfer::STATUS_SHIPPED, StockTransfer::STATUS_RECEIVED, StockTransfer::STATUS_DECLINED])) {
            event(new StockTransferUpdatedEvent($stockTransfer, $data['status']));
        }

        return $stockTransfer;
    }

    /**
     * Update from branch stock
     *
     * @param StockTransferProduct $stockTransferProduct
     * @param bool $revertTransfer
     * @return void
     */
    private function updateFromBranchStock(StockTransferProduct $stockTransferProduct, $revertTransfer = false): void
    {
        $stockRepository = app(StockRepository::class);
        $stockLogRepository = app(StockLogRepository::class);

        $stockData = [
            'productId' => $stockTransferProduct->productId,
            'branchId' => $stockTransferProduct->fromBranchId,
        ];
        if (isset($stockTransferProduct->sku)) {
            $stockData['sku'] = $stockTransferProduct->sku;
        }
        $fromBranchStock = $stockRepository->findOneBy($stockData);

        if (!$fromBranchStock instanceof Stock) {
            throw ValidationException::withMessages(['fromBranchId' => 'From branch stock is not found.']);
        }
        if (!$revertTransfer && $fromBranchStock->quantity < $stockTransferProduct->quantity) {
            throw ValidationException::withMessages(['fromBranchId' => 'Stock is not available to transfer from the branch']);
        }

        //TODO add log if to branch is not franchise
        $fromBranchStockPreviousQuantity = $fromBranchStock->quantity;
        $fromBranchStockData['quantity'] = $revertTransfer ? $fromBranchStock->quantity + $stockTransferProduct->quantity : $fromBranchStock->quantity - $stockTransferProduct->quantity;
        $updateFromBranchStock = $stockRepository->update($fromBranchStock, $fromBranchStockData);

        $stockLogRepository->save([
            'stockId' => $fromBranchStock->id,
            'productId' => $stockTransferProduct->productId,
            'resourceId' => $stockTransferProduct->id,
            'type' => $revertTransfer ? StockLog::TYPE_STOCK_TRANSFER_REVERT_FROM_BRANCH : StockLog::TYPE_STOCK_TRANSFER_FROM_BRANCH,
            'prevQuantity' => $fromBranchStockPreviousQuantity, //new code
            'newQuantity' => $stockTransferProduct->quantity,
            'quantity' => $updateFromBranchStock->quantity,
            'date' => $stockTransferProduct->created_at->format('Y-m-d'),
        ]);
    }

    /**
     * Update to branch stock
     *
     * @param StockTransferProduct $stockTransferProduct
     * @return void
     * @throws ValidationException
     */
    private function updateToBranchStock(StockTransferProduct $stockTransferProduct): void
    {
        $stockRepository = app(StockRepository::class);
        $stockLogRepository = app(StockLogRepository::class);

        $stockData = [
            'productId' => $stockTransferProduct->productId,
        ];
        if (isset($stockTransferProduct->sku)) {
            $stockData['sku'] = $stockTransferProduct->sku;
        }

        $fromBranchStock = $stockRepository->findOneBy(array_merge($stockData, ['branchId' => $stockTransferProduct->fromBranchId]), true); // get thrashed stock if from stock deleted for any reason

        $fromBranch = $stockTransferProduct->fromBranch;
        $toBranch = $stockTransferProduct->toBranch;

        if (!$fromBranchStock instanceof Stock) {
            throw ValidationException::withMessages(['toBranchId' => 'Having issue with stock transfer. Please contact with admin']);
        }

        $updateSku = false;
        $unitCost = $fromBranchStock->unitCost;
        $createdFromResourceId = null;

        if((in_array($fromBranch->type, [Branch::TYPE_SELF, Branch::TYPE_WAREHOUSE])) && $toBranch->type === Branch::TYPE_FRANCHISE) {
//            $unitCost = (float) $fromBranchStock->unitCost + (((float) $stockTransferProduct->increaseCostPriceAmount * (float) $fromBranchStock->unitCost)) / 100;

            $unitCost = $stockTransferProduct->unitCostToBranch;

            $toBranchStock = $stockRepository->findOneBy([
                'productId' => $stockTransferProduct->productId,
                'branchId' => $stockTransferProduct->toBranchId,
                'unitCost' => $unitCost,
                'unitPrice' => $fromBranchStock->unitPrice,
                'expiredDate' => $fromBranchStock->expiredDate,
                'productVariationId' => $fromBranchStock->productVariationId,
            ], true);

            if(!$toBranchStock instanceof Stock) {
                $createdFromResourceId = $fromBranchStock->id;
                $updateSku = true;
            }
        } elseif ($fromBranch->type === Branch::TYPE_FRANCHISE && in_array($toBranch->type, [Branch::TYPE_SELF, Branch::TYPE_WAREHOUSE])) {
            if(!is_null($fromBranchStock->createdFromResourceId)) {
                $toBranchStock = $stockRepository->findOne($fromBranchStock->createdFromResourceId, true); // get thrashed stock if from stock deleted for any reason
            } else {
                $extendedSku = explode('-', $stockTransferProduct->sku); // Split the string into an array, limiting to 4 parts
                if(count($extendedSku) > 3) {
                    $sku = implode('-', array_slice($extendedSku, 0, count($extendedSku) - 1)); // Reconstruct the string with the first three parts
                } else {
                    $sku = $stockTransferProduct->sku;
                }

                $toBranchStock = $stockRepository->findOneBy([
                    'productId' => $stockTransferProduct->productId,
                    'branchId' => $stockTransferProduct->toBranchId,
                    'sku' => $sku
                ], true); // get thrashed stock if from stock deleted for any reason
            }
            $unitCost = $toBranchStock->unitCost ?? 0;
        } else {
            $toBranchStock = $stockRepository->findOneBy(array_merge($stockData, ['branchId' => $stockTransferProduct->toBranchId]), true);
        }

        if($fromBranch->type == Branch::TYPE_FRANCHISE && in_array($toBranch->type, [Branch::TYPE_SELF, Branch::TYPE_WAREHOUSE]) && !$toBranchStock instanceof Stock) {
            throw ValidationException::withMessages(['toBranchId' => 'Having issue with stock transfer. Please contact with admin']);
        }

        DB::beginTransaction();

        if ($toBranchStock instanceof Stock && !$updateSku) {
            if($toBranchStock->trashed()) {
                $toBranchStock->restore();
            }

            $toBranchStockPreviousQuantity = $toBranchStock->quantity;
            $toBranchStockData['quantity'] = $toBranchStock->quantity + $stockTransferProduct->quantity;
            $toBranchStockData['unitCost'] = $unitCost;
            $toBranchStockData['unitPrice'] = $fromBranchStock->unitPrice;
            $toBranchStockData['expiredDate'] = $fromBranchStock->expiredDate;
            $toBranchStockData['productVariationId'] = $fromBranchStock->productVariationId;

            $updateToBranchStock = $stockRepository->update($toBranchStock, $toBranchStockData);

            $stockLogRepository->save([
                'stockId' => $toBranchStock->id,
                'productId' => $stockTransferProduct->productId,
                'resourceId' => $stockTransferProduct->id,
                'type' => StockLog::TYPE_STOCK_TRANSFER_TO_BRANCH,
                'prevQuantity' => $toBranchStockPreviousQuantity, // new code
                'newQuantity' => $stockTransferProduct->quantity,
                'quantity' => $updateToBranchStock->quantity,
                'date' => $stockTransferProduct->created_at->format('Y-m-d'),
            ]);
        } else {
            $sku = $fromBranchStock->sku;

            if($updateSku) {
                $sku = $fromBranchStock->sku . '-' . $stockTransferProduct->id;
            }

            $stockData = [
                'branchId' => $stockTransferProduct->toBranchId,
                'productId' => $stockTransferProduct->productId,
                'quantity' => $stockTransferProduct->quantity,
                'alertQuantity' => $fromBranchStock->alertQuantity ?? 0,
                'sku' => $sku,
                'createdFromResourceId' => $createdFromResourceId,
                'unitCost' => $unitCost,
                'unitPrice' => $fromBranchStock->unitPrice ?? 0,
                'status' => Stock::STATUS_AVAILABLE,
            ];

            if (isset($fromBranchStock->expiredDate)) {
                $stockData['expiredDate'] = $fromBranchStock->expiredDate;
            }
            if (isset($fromBranchStock->productVariationId)) {
                $stockData['productVariationId'] = $fromBranchStock->productVariationId;
            }

            $toBranchNewStock = $stockRepository->save($stockData);

            $stockLogRepository->save([
                'stockId' => $toBranchNewStock->id,
                'productId' => $toBranchNewStock->productId,
                'resourceId' => $stockTransferProduct->id,
                'type' => StockLog::TYPE_STOCK_TRANSFER_TO_BRANCH,
                'prevQuantity' => 0,
                'newQuantity' => $stockTransferProduct->quantity,
                'quantity' => $toBranchNewStock->quantity,
                'date' => $stockTransferProduct->created_at->format('Y-m-d'),
            ]);
        }

        DB::commit();
    }


    /**
     * @param array $data
     * @return mixed
     */
    private function  saveAndGetDelivery(array $data)
    {
        $deliveryRepository = app(DeliveryRepository::class);
        $deliveryData = [];
        $deliveryData['type'] = Delivery::DELIVERY_TYPE_STOCK_TRANSFER;
        $deliveryData['deliveryAgencyId'] = $data['deliveryAgencyId'] ?? null; // default "In House"
        $deliveryData['deliveryPersonName'] = $data['deliveryPersonName'] ?? null;
        $deliveryData['deliveryPersonId'] = $data['deliveryPersonId'] ?? null;
        $deliveryData['note'] = $data['sendingNote'] ?? null;
        $deliveryData['trackingNumber'] = $data['deliveryTrackingNumber'] ?? null;
        $deliveryData['fromDeliveryPhone'] = $data['fromDeliveryPhone'] ?? null;
        $deliveryData['status'] = $data['status'] ?? StockTransfer::STATUS_PENDING;

        return $deliveryRepository->save($deliveryData);
    }
}
