<?php


namespace App\Repositories;


use App\Models\Payment;
use App\Models\Purchase;
use App\Models\PurchaseProduct;
use App\Models\Supplier;
use App\Repositories\Contracts\PaymentRepository;
use App\Repositories\Contracts\PurchaseProductReturnRepository;
use App\Repositories\Contracts\PurchaseRepository;
use App\Repositories\Contracts\SupplierRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentSupplierRepository extends EloquentBaseRepository implements SupplierRepository
{

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        $queryBuilder = $this->model;
        $purchaseRepository = app(PurchaseRepository::class);

        if (isset($searchCriteria['endDate'])) {
            $queryBuilder  =  $queryBuilder->whereDate('created_at', '<=', Carbon::parse($searchCriteria['endDate']));
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder =  $queryBuilder->whereDate('created_at', '>=', Carbon::parse($searchCriteria['startDate']));
            unset($searchCriteria['startDate']);
        }

        if (isset($searchCriteria['paymentStatus'])){
            $queryForPaymentStatus = $queryBuilder->get()->map(function ($item) use ($searchCriteria){
                if ($item->paymentStatus() == $searchCriteria['paymentStatus']){
                    return $item;
                }
            })->pluck('id')->toArray();
            $queryBuilder = $queryBuilder->whereIn('id', $queryForPaymentStatus);
            unset($searchCriteria['paymentStatus']);
        }

        if (isset($searchCriteria['paymentStatusGroup'])){
            $allPaymentStatus = explode(',', $searchCriteria['paymentStatusGroup']);
            $queryForPaymentStatus = $queryBuilder->get()->map(function ($item) use ($allPaymentStatus){
                if (in_array($item->paymentStatus(), $allPaymentStatus)){
                    return $item;
                }
            })->pluck('id')->toArray();
            $queryBuilder = $queryBuilder->whereIn('id', $queryForPaymentStatus);
            unset($searchCriteria['paymentStatusGroup']);
        }


        //Purchase date wise supplier filter
        $purchaseQueryData = [];
        if (isset($searchCriteria['purchaseEndDate']) || isset($searchCriteria['purchaseStartDate'])) {
            $convertSearchCriteriaIdToArray = [];
            if(isset($searchCriteria['purchaseEndDate'])) {
                $purchaseQueryData['endDate'] = $searchCriteria['purchaseEndDate'];
            }
            if(isset($searchCriteria['purchaseStartDate'])) {
                $purchaseQueryData['startDate'] = $searchCriteria['purchaseStartDate'];
            }

            $supplierIds = $purchaseRepository
                ->getModel()
                ->where('created_at', '>=', $purchaseQueryData['startDate'])
                ->where('created_at', '<=', $purchaseQueryData['endDate'])
                ->pluck('supplierId')->toArray();

            if (isset($searchCriteria['id'])){
                $convertSearchCriteriaIdToArray = is_string($searchCriteria['id']) ? array_map('intval', explode(',', $searchCriteria['id'])) : $searchCriteria['id'];
            }
            $searchCriteria['id'] = isset($searchCriteria['id']) ? array_intersect($convertSearchCriteriaIdToArray, $supplierIds) : $supplierIds;

            unset($searchCriteria['purchaseStartDate']);
            unset($searchCriteria['purchaseEndDate']);
        }

        $withSummary = false;
        if (!empty($searchCriteria['withSummary'])) {
            unset($searchCriteria['withSummary']);
            $withSummary = true;
        }

        $searchCriteria = $this->applyFilterInSupplierSearch($searchCriteria);

        $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $queryBuilder->orderBy($orderBy, $orderDirection);

        $queryBuilder->withSum('purchases', 'totalAmount');
        $queryBuilder->withSum('purchases', 'paid');
        $queryBuilder->withSum('purchases', 'due');

        if($withSummary) {

            $sumOfQueryData = $this->getSupplierWisePurchaseSummary($searchCriteria, $purchaseQueryData);

            $summary['totalAmount'] = round($sumOfQueryData->sum('totalAmount'),2);
            $summary['totalPaid'] = round($sumOfQueryData->sum('totalPaid'),2);
            $summary['totalDue'] = round($sumOfQueryData->sum('totalDue'),2);
            $summary['totalDiscount'] = round( $sumOfQueryData->sum('totalFinalDiscountAmount'),2);
        }

        if (empty($searchCriteria['withoutPagination'])) {
            $suppliers =  $queryBuilder->paginate($limit);
        } else {
            $suppliers = $queryBuilder->get();
        }

        $pageWiseSummary = [];

        $pageWiseSummary['totalAmount'] = $suppliers->sum('purchases_sum_total_amount');
        $pageWiseSummary['totalPaid'] = $suppliers->sum('purchases_sum_paid');
        $pageWiseSummary['totalDue'] = $suppliers->sum('purchases_sum_due');

        if ($withSummary){
            return ['suppliers' => $suppliers, 'summary' => $summary, 'pageWiseSummary' => $pageWiseSummary];
        }

        return ['suppliers' => $suppliers, 'pageWiseSummary' => $pageWiseSummary];

    }

    /**
     * shorten the search based on search criteria
     *
     * @param array $searchCriteria
     * @return mixed
     */
    public function applyFilterInSupplierSearch(array $searchCriteria = [])
    {
        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('email', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('phone', 'like', '%' . $searchCriteria['query'] . '%')
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }

        if (isset($searchCriteria['branchId'])) {
            $convertSearchCriteriaIdToArray = [];
            $purchaseRepository = app(PurchaseRepository::class);
            $supplierIds = $purchaseRepository->model->where('branchId', $searchCriteria['branchId'])->pluck('supplierId')->toArray();
            if (isset($searchCriteria['id'])){
                $convertSearchCriteriaIdToArray = is_string($searchCriteria['id']) ? array_map('intval', explode(',', $searchCriteria['id'])) : $searchCriteria['id'];
            }
            $searchCriteria['id'] = isset($searchCriteria['id']) ? array_intersect($convertSearchCriteriaIdToArray, $supplierIds) : $supplierIds;

            unset($searchCriteria['branchId']);
        }

        if (isset($searchCriteria['id'])) {
            $searchCriteria['id'] = is_array($searchCriteria['id']) ? implode(",", array_unique($searchCriteria['id'])) : $searchCriteria['id'];
        }

        return $searchCriteria;
    }

    /**
     * @param array $searchCriteria
     * @return Collection
     */
    public function calculateSupplierPurchaseDetails(array $searchCriteria = []): Collection
    {
        $purchaseRepository = app(PurchaseRepository::class);
        $purchaseModelTable = $purchaseRepository->getModel()->getTable();
        $thisModelTable = $this->model->getTable();

        $querySupplierId = isset($searchCriteria['supplierId']) ? $thisModelTable . '.id' . '='. $searchCriteria['supplierId'] : '1 = 1';
        $queryBranchId = isset($searchCriteria['branchId']) ? $purchaseModelTable . '.branchId' . '='. $searchCriteria['branchId'] : '1 = 1';
        $queryEndDate = isset($searchCriteria['endDate']) ? $purchaseModelTable . '.created_at' . '<=' . $searchCriteria['endDate'] : '1 = 1';
        $queryStartDate = isset($searchCriteria['startDate']) ? $purchaseModelTable . '.created_at' . '>=' . $searchCriteria['startDate'] : '1 = 1';

        return DB::table($thisModelTable)
            ->select($thisModelTable . '.id as supplierId')
            ->whereRaw(DB::raw($querySupplierId))
            ->join($purchaseModelTable, $thisModelTable . '.id', '=', $purchaseModelTable . '.supplierId')
            ->selectRaw("SUM(" .$purchaseModelTable. ".totalAmount) as totalAmount")
            ->selectRaw("SUM(" .$purchaseModelTable. ".due) as totalDue")
            ->selectRaw("SUM(" .$purchaseModelTable. ".paid) as totalPaid")
            ->selectRaw("SUM(" .$purchaseModelTable. ".shippingCost) as totalShippingCost")
            ->selectRaw("SUM(" .$purchaseModelTable. ".discountAmount) as totalDiscount")
            ->selectRaw("SUM(" .$purchaseModelTable. ".taxAmount) as totalTax")
            ->whereRaw(DB::raw($queryBranchId))
            ->whereRaw(DB::raw($queryEndDate))
            ->whereRaw(DB::raw($queryStartDate))
            ->groupBy($thisModelTable . '.id')
            ->get();
    }

    /**
     * @param array $searchCriteria
     * @return Collection
     */
    public function calculateSupplierPurchaseReturnDetails(array $searchCriteria = []): Collection
    {
        $purchaseRepository = app(PurchaseRepository::class);
        $purchaseModelTable = $purchaseRepository->getModel()->getTable();

        $purchaseProductReturnRepository = app(PurchaseProductReturnRepository::class);
        $purchaseProductReturnModelTable = $purchaseProductReturnRepository->getModel()->getTable();

        $thisModelTable = $this->model->getTable();

        $querySupplierId = isset($searchCriteria['supplierId']) ? $thisModelTable . '.id' . '='. $searchCriteria['supplierId'] : '1 = 1';
        $queryBranchId = isset($searchCriteria['branchId']) ? $purchaseModelTable . '.branchId' . '='. $searchCriteria['branchId'] : '1 = 1';
        $queryEndDate = isset($searchCriteria['endDate']) ? $purchaseProductReturnModelTable . '.created_at' . '<=' . $searchCriteria['endDate'] : '1 = 1';
        $queryStartDate = isset($searchCriteria['startDate']) ? $purchaseProductReturnModelTable . '.created_at' . '>=' . $searchCriteria['startDate'] : '1 = 1';

        return DB::table($thisModelTable)
            ->select($thisModelTable . '.id as supplierId')
            ->whereRaw(DB::raw($querySupplierId))
            ->join($purchaseModelTable, $thisModelTable . '.id', '=', $purchaseModelTable . '.supplierId')
            ->join($purchaseProductReturnModelTable, $purchaseModelTable . '.id', '=', $purchaseProductReturnModelTable . '.purchaseId')
            ->selectRaw("count(*) as totalReturn")
            ->selectRaw("SUM(" .$purchaseProductReturnModelTable. ".returnAmount) as totalReturnAmount")
            ->selectRaw("SUM(" .$purchaseProductReturnModelTable. ".quantity) as totalReturnQuantity")
            ->whereRaw(DB::raw($queryBranchId))
            ->whereRaw(DB::raw($queryEndDate))
            ->whereRaw(DB::raw($queryStartDate))
            ->groupBy($thisModelTable . '.id')
            ->get();
    }

    /**
     * @param $searchCriteria
     * @param $purchaseQueryData
     */
    public function getSupplierWisePurchaseSummary($searchCriteria, $purchaseQueryData)
    {
        $thisModelTable = $this->model->getTable();
        $purchaseModelTable = Purchase::getTableName();
        $purchaseProductModelTable = PurchaseProduct::getTableName();

        return DB::table($thisModelTable)
            ->select(
                DB::raw($thisModelTable . '.id as supplierId'),
                DB::raw('SUM(' . $purchaseModelTable.'.totalAmount) as totalAmount'),
                DB::raw('SUM(' . $purchaseModelTable.'.paid) as totalPaid'),
                DB::raw('SUM(' . $purchaseModelTable.'.due) as totalDue'),
                DB::raw('SUM(' . $purchaseProductModelTable.'.finalDiscountAmount) as totalFinalDiscountAmount')
            )
            ->leftJoin($purchaseModelTable, function ($join) use($searchCriteria, $thisModelTable, $purchaseModelTable, $purchaseQueryData) {
                $join->on($thisModelTable . '.id', '=', $purchaseModelTable . '.supplierId')
                    ->when(isset($searchCriteria['branchId']), function ($query) use($searchCriteria, $purchaseModelTable) {
                        $query->where($purchaseModelTable . '.branchId', '=', $searchCriteria['branchId']);
                    })
                    ->when(isset($purchaseQueryData['startDate']), function ($query) use ($purchaseModelTable, $purchaseQueryData) {
                        $query->whereDate( $purchaseModelTable .'.created_at', '<=', $purchaseQueryData['endDate'])
                            ->whereDate( $purchaseModelTable .'.created_at', '>=', $purchaseQueryData['startDate']);
                    });
            })
            ->leftJoin($purchaseProductModelTable, $purchaseModelTable . '.id', '=', $purchaseProductModelTable . '.purchaseId')
            ->when(isset($searchCriteria['id']), function ($query) use ($searchCriteria, $thisModelTable) {
                $query->whereIn($thisModelTable . '.id', explode(',', $searchCriteria['id']));
            })
            ->groupBy('supplierId')
            ->get();

    }

    /**
     * @param array $data
     * @return \ArrayAccess|null
     */
    public function paySupplierDue(array $data): ?\ArrayAccess
    {
        $supplier = $this->findOne($data['supplierId']);
        DB::beginTransaction();
        if (in_array($supplier->paymentStatus(), [Payment::PAYMENT_STATUS_UNPAID, Payment::PAYMENT_STATUS_PARTIAL])) {
            $purchaseRepo = app(PurchaseRepository::class);
            $paidAmount = $data['paidAmount'];
            foreach ($supplier->duePurchase as $purchase) {
                if ($purchase instanceof Purchase) {
                    //Calculate paid amount for due payment
                    if ($purchase->due >= $paidAmount) {
                        $newPaidAmount = $purchase->paid + $paidAmount;
                        $newDueAmount = $purchase->due - $paidAmount;
                        $paymentStatus = Payment::paymentStatus($newDueAmount, $newPaidAmount);
                        $paymentAmount = $paidAmount;
                    } else {
                        $newPaidAmount = $purchase->paid + $purchase->due;
                        $newDueAmount = 0;
                        $paymentStatus = Payment::paymentStatus($newDueAmount, $newPaidAmount);
                        $paymentAmount = $purchase->due;
                    }

                    $paidAmount -= $paymentAmount;

                    if ($purchase->paid < $newPaidAmount) {

                        //Update Supplier Purchase Due Details.
                        $purchaseRepo->update($purchase, [
                            'paid'          => $newPaidAmount,
                            'due'           => $newDueAmount,
                            'paymentStatus' => $paymentStatus,
                        ]);

                        $paymentData = [
                            'amount'           => $paymentAmount,
                            'method'           => $data['method'],
                            'txnNumber' => isset($data['txnNumber']) ? $data['txnNumber'] : null,
                            'referenceNumber' => isset($data['referenceNumber']) ? $data['referenceNumber'] : null,
                            'cashFlow' => Payment::CASH_FLOW_OUT,
                            'paymentableId'       => $purchase->id,
                            'paymentableType'       => Payment::PAYMENT_SOURCE_PURCHASE,
                            'payType'       => Payment::PAY_TYPE_PURCHASE_DUE,
                            'status'           => Payment::STATUS_SUCCESS,
                            'receivedByUserId' => auth()->id(),
                            'date' => Carbon::now()
                        ];

                        $paymentRepo = app(PaymentRepository::class);
                        $paymentRepo->saveOnlyPayment($paymentData);
                    }
                }
            }
        }
        DB::commit();
        return $supplier;
    }
}
