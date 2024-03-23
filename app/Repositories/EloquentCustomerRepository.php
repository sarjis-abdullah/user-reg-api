<?php

namespace App\Repositories;


use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\OrderProductReturnRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\PaymentRepository;
use App\Repositories\Contracts\PurchaseProductReturnRepository;
use App\Repositories\Contracts\PurchaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentCustomerRepository extends EloquentBaseRepository implements CustomerRepository
{
    /**
     * @inheritDoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        $queryBuilder = $this->model;
        $orderRepository = app(OrderRepository::class);

        if (isset($searchCriteria['endDate'])) {
            $ $queryBuilder  =  $queryBuilder->whereDate('created_at', '<=', Carbon::parse($searchCriteria['endDate']));
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder =  $queryBuilder->whereDate('created_at', '>=', Carbon::parse($searchCriteria['startDate']));
            unset($searchCriteria['startDate']);
        }

        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('email', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('phone', 'like', '%' . $searchCriteria['query'] . '%')
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
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

        if(isset($searchCriteria['isDisabledBranchIdFilter'])) {
           unset($searchCriteria['isDisabledBranchIdFilter']);
           unset($searchCriteria['branchId']);
        }
        if (isset($searchCriteria['branchId'])){
            unset($searchCriteria['branchId']);
        }

        $orderQueryData = [];

        //order date wise customer filter
        if (isset($searchCriteria['orderEndDate']) || isset($searchCriteria['orderStartDate'])) {

            if(isset($searchCriteria['orderEndDate'])) {
                $orderQueryData['endDate'] = $searchCriteria['orderEndDate'];
            }
            if(isset($searchCriteria['orderStartDate'])) {
                $orderQueryData['startDate'] = $searchCriteria['orderStartDate'];
            }

            $customerIds = $orderRepository->getModel()
                ->where('created_at', '>=', $orderQueryData['startDate'])
                ->where('created_at', '<=', $orderQueryData['endDate'])
                ->pluck('customerId')
                ->toArray();

            $searchCriteria['id'] = isset($searchCriteria['id']) ? array_intersect(array($searchCriteria['id']), $customerIds) : $customerIds;

            unset($searchCriteria['orderStartDate']);
            unset($searchCriteria['orderEndDate']);
        }

        $withSummary = false;
        if (!empty($searchCriteria['withSummary'])) {
            unset($searchCriteria['withSummary']);
            $withSummary = true;
        }

        $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $queryBuilder->orderBy($orderBy, $orderDirection);

        $searchCriteria['eagerLoad'] = [
            'customer.createdByUser' => 'createdByUser',
            'customer.branch' => 'branch',
            'customer.loyaltyRewards' => 'loyaltyRewards',
            'customer.orders' => 'orders',
            'customer.dueOrders' => 'dueOrders',
            'customer.updatedByUser' => 'updatedByUser'
        ];

        $queryBuilder = $this->applyEagerLoad($queryBuilder, $searchCriteria);

        if($withSummary) {
            $sumOfQueryData = $this->getCustomerrWiseOrderSummary($searchCriteria, $orderQueryData);
            $summary['totalAmount'] = round($sumOfQueryData->sum('totalAmount'),2);
            $summary['totalPaid'] = round($sumOfQueryData->sum('totalPaid'),2);
            $summary['totalDue'] = round($sumOfQueryData->sum('totalDue'),2);
            $summary['totalDiscount'] = round( $sumOfQueryData->sum('totalDiscount'),2);
        }
            $queryBuilder->withSum('orders', 'amount');
            $queryBuilder->withSum('orders', 'paid');
            $queryBuilder->withSum('orders', 'due');
            $queryBuilder->withSum('orders', 'discount');

        if (empty($searchCriteria['withoutPagination'])) {
            $customers =  $queryBuilder->paginate($limit);
        } else {
            $customers = $queryBuilder->get();
        }
        $pageWiseSummary = [];

        $pageWiseSummary['totalAmount'] = $customers->sum('orders_sum_amount');
        $pageWiseSummary['totalPaid'] = $customers->sum('orders_sum_paid');
        $pageWiseSummary['totalDue'] = $customers->sum('orders_sum_due');
        $pageWiseSummary['totalDiscount'] = $customers->sum('orders_sum_discount');

        if ($withSummary){
            return ['customers' => $customers, 'summary' => $summary, 'pageWiseSummary' => $pageWiseSummary];
        }

        return ['customers' => $customers, 'pageWiseSummary' => $pageWiseSummary];
    }

    /**
     * @param array $searchCriteria
     * @return Collection
     */
    public function calculateCustomerOrderDetails(array $searchCriteria = []): Collection
    {
        $orderRepository = app(OrderRepository::class);
        $orderModelTable = $orderRepository->getModel()->getTable();
        $thisModelTable = $this->model->getTable();

        $queryCustomerId = isset($searchCriteria['customerId']) ? $thisModelTable . '.id' . '='. $searchCriteria['customerId'] : '1 = 1';
        $queryBranchId = isset($searchCriteria['branchId']) ? $orderModelTable . '.branchId' . '='. $searchCriteria['branchId'] : '1 = 1';
        $queryEndDate = isset($searchCriteria['endDate']) ? $orderModelTable . '.created_at' . '<=' . $searchCriteria['endDate'] : '1 = 1';
        $queryStartDate = isset($searchCriteria['startDate']) ? $orderModelTable . '.created_at' . '>=' . $searchCriteria['startDate'] : '1 = 1';

        return DB::table($thisModelTable)
            ->select($thisModelTable . '.id as customerId')
            ->whereRaw(DB::raw($queryCustomerId))
            ->join($orderModelTable, $thisModelTable . '.id', '=', $orderModelTable . '.customerId')
            ->selectRaw("SUM(" .$orderModelTable. ".amount) as totalAmount")
            ->selectRaw("SUM(" .$orderModelTable. ".due) as totalDue")
            ->selectRaw("SUM(" .$orderModelTable. ".paid) as totalPaid")
            ->selectRaw("SUM(" .$orderModelTable. ".shippingCost) as totalShippingCost")
            ->selectRaw("SUM(" .$orderModelTable. ".discount) as totalDiscount")
            ->selectRaw("SUM(" .$orderModelTable. ".tax) as totalTax")
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
    public function calculateCustomerOrderReturnDetails(array $searchCriteria = []): Collection
    {
        $orderRepository = app(OrderRepository::class);
        $orderModelTable = $orderRepository->getModel()->getTable();

        $orderProductReturnRepository = app(OrderProductReturnRepository::class);
        $orderProductReturnModelTable = $orderProductReturnRepository->getModel()->getTable();

        $thisModelTable = $this->model->getTable();

        $queryCustomerId = isset($searchCriteria['customerId']) ? $thisModelTable . '.id' . '='. $searchCriteria['customerId'] : '1 = 1';
        $queryBranchId = isset($searchCriteria['branchId']) ? $orderModelTable . '.branchId' . '='. $searchCriteria['branchId'] : '1 = 1';
        $queryEndDate = isset($searchCriteria['endDate']) ? $orderProductReturnModelTable . '.created_at' . '<=' . $searchCriteria['endDate'] : '1 = 1';
        $queryStartDate = isset($searchCriteria['startDate']) ? $orderProductReturnModelTable . '.created_at' . '>=' . $searchCriteria['startDate'] : '1 = 1';

        return DB::table($thisModelTable)
            ->select($thisModelTable . '.id as customerId')
            ->whereRaw(DB::raw($queryCustomerId))
            ->join($orderModelTable, $thisModelTable . '.id', '=', $orderModelTable . '.customerId')
            ->join($orderProductReturnModelTable, $orderModelTable . '.id', '=', $orderProductReturnModelTable . '.orderId')
            ->selectRaw("count(*) as totalReturn")
            ->selectRaw("SUM(" .$orderProductReturnModelTable. ".returnAmount) as totalReturnAmount")
            ->selectRaw("SUM(" .$orderProductReturnModelTable. ".quantity) as totalReturnQuantity")
            ->whereRaw(DB::raw($queryBranchId))
            ->whereRaw(DB::raw($queryEndDate))
            ->whereRaw(DB::raw($queryStartDate))
            ->groupBy($thisModelTable . '.id')
            ->get();
    }

    public function getCustomerrWiseOrderSummary($searchCriteria, $orderQueryData)
    {
        $thisModelTable = $this->model->getTable();
        $orderModelTable = Order::getTableName();

        return DB::table($thisModelTable)
            ->select(
                DB::raw($thisModelTable . '.id as customerId'),
                DB::raw('SUM(' . $orderModelTable.'.amount) as totalAmount'),
                DB::raw('SUM(' . $orderModelTable.'.paid) as totalPaid'),
                DB::raw('SUM(' . $orderModelTable.'.due) as totalDue'),
                DB::raw('SUM(' . $orderModelTable.'.discount) as totalDiscount'),
            )
            ->leftJoin($orderModelTable, function ($join) use($searchCriteria, $thisModelTable, $orderModelTable, $orderQueryData) {
                $join->on($thisModelTable . '.id', '=', $orderModelTable . '.customerId')
                    ->when(request()->filled('branchId'), function ($query) use($orderModelTable) {
                        $query->where($orderModelTable . '.branchId', '=', request()->get('branchId'));
                    })
                    ->when(isset($orderQueryData['endDate']), function ($query) use ($orderModelTable, $orderQueryData) {
                        $query->whereDate( $orderModelTable .'.created_at', '<=', $orderQueryData['endDate'])
                            ->whereDate( $orderModelTable .'.created_at', '>=', $orderQueryData['startDate']);
                    });
            })
            ->when(isset($searchCriteria['id']), function ($query) use ($searchCriteria, $thisModelTable) {
                $convertSearchCriteriaIdToArray = is_string($searchCriteria['id']) ? array_map('intval', explode(',', $searchCriteria['id'])) : $searchCriteria['id'];
                $query->whereIn($thisModelTable . '.id', $convertSearchCriteriaIdToArray);
            })
            ->groupBy('customerId')
            ->get();

    }

    /**
     * @param $data
     * @return \ArrayAccess|null
     */
    public function payCustomerDue($data): ?\ArrayAccess
    {
        $customer = $this->findOne($data['customerId']);
        DB::beginTransaction();
        if (in_array($customer->paymentStatus(), [Payment::PAYMENT_STATUS_UNPAID, Payment::PAYMENT_STATUS_PARTIAL])){
            $orderRepository = app(OrderRepository::class);
            $paidAmount = $data['paidAmount'];
            foreach ($customer->dueOrders as $order){
                if ($order instanceof Order){
                    //Calculate paid amount for due payment
                    if ($order->due >= $paidAmount){
                        $newPaidAmount = $order->paid + $paidAmount;
                        $newDueAmount = $order->due - $paidAmount;
                        $paymentStatus = Payment::paymentStatus($newDueAmount, $newPaidAmount);
                        $paymentAmount = $paidAmount;
                    }else{
                        $newPaidAmount = $order->paid + $order->due;
                        $newDueAmount = 0;
                        $paymentStatus = Payment::paymentStatus($newDueAmount, $newPaidAmount);
                        $paymentAmount = $order->due;
                    }

                    $paidAmount -= $paymentAmount;

                    //Update Customer Order Due Details.
                    if ($order->paid < $newPaidAmount){
                        $orderRepository->update($order, [
                            'paid' => $newPaidAmount,
                            'due' => $newDueAmount,
                            'paymentStatus' => $paymentStatus,
                        ]);

                        $paymentData = [
                            'amount' => $paymentAmount,
                            'receivedAmount' => $paymentAmount,
                            'method' => $data['method'],
                            'txnNumber' => isset($data['txnNumber']) ? $data['txnNumber'] : null,
                            'referenceNumber' => isset($data['referenceNumber']) ? $data['referenceNumber'] : null,
                            'cashFlow' => Payment::CASH_FLOW_IN,
                            'paymentableId' => $order->id,
                            'paymentableType' => Payment::PAYMENT_SOURCE_ORDER,
                            'payType' => Payment::PAY_TYPE_ORDER_DUE,
                            'status' => Payment::STATUS_SUCCESS,
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

        return $customer;
    }
}
