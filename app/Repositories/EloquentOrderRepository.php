<?php


namespace App\Repositories;

use App\Events\Order\OrderCreatedEvent;
use App\Events\OrderProductReturn\OrderProductReturnCreatedEvent;
use App\Events\Woocommerce\OrderUpdatingEvent;
use App\Events\Woocommerce\StockSavingEvent;
use App\Models\Branch;
use App\Models\CustomerLoyaltyReward;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\Stock;
use App\Models\StockLog;
use App\Repositories\Contracts\CustomerLoyaltyRewardRepository;
use App\Repositories\Contracts\OrderLogRepository;
use App\Repositories\Contracts\OrderProductRepository;
use App\Repositories\Contracts\OrderProductReturnRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\PaymentRepository;
use App\Repositories\Contracts\QuotationRepository;
use App\Repositories\Contracts\StockLogRepository;
use App\Repositories\Contracts\StockRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EloquentOrderRepository extends EloquentBaseRepository implements OrderRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        $queryBuilder = $this->model->newQuery();

        if (isset($searchCriteria['endDate'])) {
            $queryBuilder  =  $queryBuilder->whereDate('created_at', '<=', Carbon::parse($searchCriteria['endDate'])->endOfDay());
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder =  $queryBuilder->whereDate('created_at', '>=', Carbon::parse($searchCriteria['startDate'])->startOfDay());
            unset($searchCriteria['startDate']);
        }

        if (isset($searchCriteria['paymentStatusGroup'])){
            $allPaymentStatus = explode(',', $searchCriteria['paymentStatusGroup']);
            $queryBuilder = $queryBuilder->whereIn('paymentStatus', $allPaymentStatus);
            unset($searchCriteria['paymentStatusGroup']);
        }

        if (isset($searchCriteria['paymentMethod'])){
            $queryBuilder = $queryBuilder->whereHas('payments', fn($q) => $q->where('method', $searchCriteria['paymentMethod']));
            unset($searchCriteria['paymentMethod']);
        }

        $withSummary = !empty($searchCriteria['withSummary']);
        unset($searchCriteria['withSummary']);

        $searchCriteria = $this->applyFilterInOrderSearch($searchCriteria);

        $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $queryBuilder->orderBy($orderBy, $orderDirection);

        $searchCriteria['eagerLoad'] = [
            'order.orderProducts' => 'orderProducts',
            'order.logs' => 'orderLogs',
            'order.branch' => 'branch',
            'order.payments' => 'payments',
            'order.invoiceImage' => 'invoiceImage',
            'order.customer' => 'customer',
            'order.salePerson' => 'salePerson',
            'order.createdByUser' => 'createdByUser',
            'order.orderProductReturns' => 'orderProductReturns',
            'op.product' => 'orderProducts.product',
            'op.discount' => 'orderProducts.getDiscount',
            'op.tax' => 'orderProducts.getTax',
            'order.coupon' => 'coupon'
        ];

        $queryBuilder = $this->applyEagerLoad($queryBuilder, $searchCriteria);

        $queryBuilder->withSum('orderProductReturns', 'returnAmount');
        $queryBuilder->withSum('orderProductReturns', 'profitAmount');
        $queryBuilder->withSum('orderProductReturns', 'discountAmount');
        $summary = [];
        if ($withSummary) {
            $summary['totalSalesAmount'] = $queryBuilder->sum('amount');
            $summary['totalPaid'] = $queryBuilder->sum('paid');
            $summary['totalDue'] = $queryBuilder->sum('due');
            $summary['totalProfit'] = round($queryBuilder->sum('profitAmount'),2);
            $summary['totalDiscount'] = round($queryBuilder->sum('discount'),2);
            $summary['totalNet'] = round($queryBuilder->sum('grossProfit'),2);

            $queryBuilderAllForTotalReturn = $queryBuilder->get();
            $summary['totalReturnAmount'] = round($queryBuilderAllForTotalReturn->sum('order_product_returns_sum_return_amount'),2);
            $summary['totalNetPaidAmount'] = round(($queryBuilder->sum('paid') - $queryBuilderAllForTotalReturn->sum('order_product_returns_sum_return_amount')),2);
            $summary['totalNetGrossProfit'] = round($queryBuilder->sum('grossProfit') - ($queryBuilderAllForTotalReturn->sum('order_product_returns_sum_profit_amount') - $queryBuilderAllForTotalReturn->sum('order_product_returns_sum_discount_amount')),2);
            $summary['totalNetTotalAmount'] = round($queryBuilder->sum('amount') - $queryBuilderAllForTotalReturn->sum('order_product_returns_sum_return_amount'), 2);
        }

        if (empty($searchCriteria['withoutPagination'])) {
            $page = !empty($searchCriteria['page']) ? (int)$searchCriteria['page'] : 1;
            $orders = $queryBuilder->paginate($limit, ['*'], 'page', $page);
        } else {
            $orders= $queryBuilder->get();
        }

        if(!empty($searchCriteria['withoutPagination'])) {
            $pageWiseSummary = $summary;
        } else {
            $pageWiseSummary['totalSalesAmount'] = round($orders->sum('amount'),2);
            $pageWiseSummary['totalPaid'] = round($orders->sum('paid'),2);
            $pageWiseSummary['totalDue'] = round($orders->sum('due'),2);
            $pageWiseSummary['totalProfit'] = round($orders->sum('profitAmount'),2);
            $pageWiseSummary['totalDiscount'] = round($orders->sum('discount'),2);
            $pageWiseSummary['totalNet'] = round($orders->sum('grossProfit'),2);

            $pageWiseSummary['totalReturnAmount'] = round($orders->sum('order_product_returns_sum_return_amount'),2);
            $pageWiseSummary['totalNetPaidAmount'] = round(($orders->sum('paid') - $orders->sum('order_product_returns_sum_return_amount')),2);
            $pageWiseSummary['totalNetGrossProfit'] = round($orders->sum('grossProfit') - ($orders->sum('order_product_returns_sum_profit_amount') - $orders->sum('order_product_returns_sum_discount_amount')),2);
            $pageWiseSummary['totalNetTotalAmount'] = round($orders->sum('amount') - $orders->sum('order_product_returns_sum_return_amount'), 2);
        }

        $result = [
            'orders' => $orders,
            'pageWiseSummary' => $pageWiseSummary,
        ];

        if ($withSummary) {
            $result['summary'] = $summary;
        }

        return $result;
    }

    /**
     * @inherit doc
     */
    public function findByOrderReturnProducts(array $searchCriteria = [], $withTrashed = false): array
    {
        $queryBuilder = $this->model;
        $orderProductReturnRepo = app(OrderProductReturnRepository::class);
        $orderProductReturnTable = $orderProductReturnRepo->getModel()->getTable();

        if (isset($searchCriteria['orderReturnEndDate'])) {
            $queryBuilder = $queryBuilder->whereHas('orderProductReturns', function ($q) use ($searchCriteria, $orderProductReturnTable){
                $q->whereDate($orderProductReturnTable.'.created_at', '<=', Carbon::parse($searchCriteria['orderReturnEndDate']));
            });
            unset($searchCriteria['orderReturnEndDate']);
        }

        if (isset($searchCriteria['orderReturnStartDate'])) {
            $queryBuilder = $queryBuilder->whereHas('orderProductReturns', function ($q) use ($searchCriteria, $orderProductReturnTable){
                $q->whereDate($orderProductReturnTable.'.created_at', '>=', Carbon::parse($searchCriteria['orderReturnStartDate']));
            });
            unset($searchCriteria['orderReturnStartDate']);
        }

        if (isset($searchCriteria['query'])) {
            $opr = app(OrderProductRepository::class);
            $productIds = $opr->model->whereHas('product', function($query) use ($searchCriteria){
                $query->where('name', 'like', '%' . $searchCriteria['query'] . '%');
            })->pluck('productId')->toArray();

            $searchCriteria['id'] = $this->model->where('invoice', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhereHas('customer', function($query) use ($searchCriteria){
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                          ->orWhere('email', 'like', '%' . $searchCriteria['query'] . '%')
                          ->orWhere('phone', 'like', '%' . $searchCriteria['query'] . '%');
                })
                ->orWhereHas('orderProducts', function($query) use ($searchCriteria, $productIds){
                    $query->whereIn('productId', $productIds);
                })
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }

        $withSummary = !empty($searchCriteria['withSummary']);
        unset($searchCriteria['withSummary']);

        $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $queryBuilder->orderBy($orderBy, $orderDirection);

        $queryBuilder->withCount('orderProductReturns')
            ->withSum('orderProductReturns', 'returnAmount', 'quantity')
            ->having('order_product_returns_count', '>', 0);

        $summary = [];

        if ($withSummary) {
            $allReturnOrdersData = $queryBuilder->get();
            $summary['totalReturnAmount'] = round($allReturnOrdersData->sum('order_product_returns_sum_return_amount'),2);
            $summary['totalReturnQuantity'] = round($allReturnOrdersData->sum('order_product_returns_sum_quantity'),2);
        }

        if ($withTrashed) {
            $queryBuilder->withTrashed();
        }

        if (empty($searchCriteria['withoutPagination'])) {
            $returnOrders = $queryBuilder->paginate($limit);
        } else {
            $returnOrders = $queryBuilder->get();
        }

        $pageWiseSummary = [];

        $pageWiseSummary['totalReturnAmount'] = round($returnOrders->sum('order_product_returns_sum_return_amount'),2);
        $pageWiseSummary['totalReturnQuantity'] = round($returnOrders->sum('order_product_returns_sum_quantity'),2);


        $result = [
            'returnOrders' => $returnOrders,
            'pageWiseSummary' => $pageWiseSummary,
        ];

        if ($withSummary) {
            $result['summary'] = $summary;
        }

        return $result;
    }

    /**
     * inherit doc
     */
    public function save(array $data): \ArrayAccess
    {
        DB::beginTransaction();

        $data['status'] = $data['status'] ?? Order::STATUS_DELIVERED;
        $data['customerId'] = $data['customerId'] ?? 1;

        $data['tax'] = collect($data['orderProducts'])->sum(function ($item) {
            return $item['tax'];
        });

        $order = parent::save($data);

        $orderProductRepository = app(OrderProductRepository::class);

        foreach ($data['orderProducts'] as $product) {
            $orderProduct = $product;
            $orderProduct['createdByUserId'] = $order->createdByUserId;
            $orderProduct['orderId'] = $order->id;
            $orderProduct['date'] = $order->date;
            $orderProduct['status'] = OrderProduct::STATUS_DELIVERED;

            $orderProductRepository->save($orderProduct);
        }

        $updatedOrderData = [];
        $hasLoyaltyRewardPayment  = null;

        if(isset($data['payment'])) {
            $paymentData = $data['payment'];
            $paymentData['status'] = Payment::STATUS_SUCCESS;
            $paymentData['cashFlow'] = Payment::CASH_FLOW_IN;
            $paymentData['paymentableId'] = $order->id;
            $paymentData['paymentableType'] = Payment::PAYMENT_SOURCE_ORDER;
            $paymentData['payType'] = Payment::PAY_TYPE_ORDER;
            $paymentData['receivedByUserId'] = $order->createdByUserId;

            $paymentRepository = app(PaymentRepository::class);
            $payment = $paymentRepository->save($paymentData);

            $updatedOrderData['paid'] = $order->paid + $payment->amount;
            $updatedOrderData['due'] = round($order->amount, 2) - round($updatedOrderData['paid'],2);
            $updatedOrderData['date'] =Carbon::now();
            $updatedOrderData['paymentStatus'] = Payment::paymentStatus($updatedOrderData['due'], $updatedOrderData['paid']);

            $hasLoyaltyRewardPayment = $payment->method === Payment::METHOD_LOYALTY_REWARD ? $payment : null;
        }

        if(isset($data['payments'])) {
            $paymentRepository = app(PaymentRepository::class);

            $payments = collect($data['payments'])->map(function ($payment) use($order, $paymentRepository) {
                $paymentData = array_merge($payment, [
                    'status' =>  Payment::STATUS_SUCCESS,
                    'cashFlow' =>  Payment::CASH_FLOW_IN,
                    'payType' =>  Payment::PAY_TYPE_ORDER,
                    'paymentableType' => Payment::PAYMENT_SOURCE_ORDER,
                    'paymentableId' => $order->id,
                    'receivedByUserId' => $order->createdByUserId,
                    'createdByUserId' => $order->createdByUserId,
                    'date' => Carbon::now(),
                ]);

                $paymentRepository->getModel()->create($paymentData);

                return $paymentData;
            });

            $updatedOrderData['paid'] = $order->paid + $payments->sum('amount');
            $updatedOrderData['due'] = round($order->amount, 2) - round($updatedOrderData['paid'],2);
            $updatedOrderData['date'] =Carbon::now();
            $updatedOrderData['paymentStatus'] = Payment::paymentStatus($updatedOrderData['due'], $updatedOrderData['paid']);

            $hasLoyaltyRewardPayment = $payments->firstWhere('method', Payment::METHOD_LOYALTY_REWARD);
        }

        if($order->amount == 0) {
            $updatedOrderData['paid'] = 0;
            $updatedOrderData['due'] = 0;
            $updatedOrderData['paymentStatus'] = Payment::paymentStatus($updatedOrderData['due'], $updatedOrderData['paid']);
        }

        //TODO: add a validation on request for check points and amount for conversions
        if($hasLoyaltyRewardPayment instanceof Payment) {
            $pointsRedeemedInThisOrder =$hasLoyaltyRewardPayment->redeemedPoints;

            $clrData = [
                'customerId' => $order->customerId,
                'loyaltyableId' => $hasLoyaltyRewardPayment->id,
                'loyaltyableType' => CustomerLoyaltyReward::TYPE_PAYMENT,
                'action' => CustomerLoyaltyReward::ACTION_REDEEMED,
                'points' => $pointsRedeemedInThisOrder,
                'amount' => $hasLoyaltyRewardPayment->amount,
            ];

            app(CustomerLoyaltyRewardRepository::class)->save($clrData);
        }

        if (!empty($data['quotationId'])){
            $quotationRepo = app(QuotationRepository::class);
            $quotation = $quotationRepo->findOne($data['quotationId']);
            if ($quotation instanceof Quotation){
                $quotationRepo->update($quotation, ['status' => Quotation::STATUS_SOLD]);
            }
        }

        app(OrderLogRepository::class)->save([
            'createdByUserId' => $order->createdByUserId,
            'orderId' => $order->id,
            'comment' => 'order is just placed.',
            'status' => $order->status,
            'paymentStatus' => is_array($updatedOrderData) && isset($updatedOrderData['paymentStatus']) ? $updatedOrderData['paymentStatus'] : '',
            'deliveryStatus' => $order->status,
        ]);

        DB::commit();

        event(new OrderCreatedEvent($order, $updatedOrderData));

        return $order;
    }

    /**
     * @param array $data
     * @return \ArrayAccess
     */
    public function saveExchange(array $data): \ArrayAccess
    {
        DB::beginTransaction();

        $data['status'] = $data['status'] ?? Order::STATUS_DELIVERED;
        $data['customerId'] = $data['customerId'] ?? 1;

        $data['tax'] = collect($data['orderProducts'])->sum(function ($item) {
            return $item['tax'];
        });

        $order = parent::save($data);
        $referenceOrder = $this->model->where('id', $data['referenceOrderId'])->first();

        $orderProductRepository = app(OrderProductRepository::class);

        foreach ($data['orderProducts'] as $product) {
            $orderProduct = $product;
            $orderProduct['createdByUserId'] = $order->createdByUserId;
            $orderProduct['orderId'] = $order->id;
            $orderProduct['date'] = $order->date;
            $orderProduct['status'] = OrderProduct::STATUS_DELIVERED;

            $orderProductRepository->save($orderProduct);
        }

        $orderProductRepository = app(OrderProductRepository::class);

        foreach ($data['orderProductReturns'] as $productReturn) {

            $orderProductModel = $orderProductRepository->getModel();
            $orderProduct = $orderProductModel->where('id', $productReturn['orderProductId'])->first(['id', 'productId', 'stockId']);

            $data['orderId'] = $order->id;
            $data['orderProductId'] = $productReturn['orderProductId'];
            $data['productId'] = $orderProduct ? $orderProduct->productId : null;
            $data['stockId'] = $orderProduct ? $orderProduct->stockId : null;
            $data['quantity'] = $productReturn['quantity'];
            $data['returnAmount'] = $productReturn['returnAmount'];

            $orderProductReturnRepository = app(OrderProductReturnRepository::class);
            $orderProductReturn = $orderProductReturnRepository->saveReturnAbleProduct($data);

            event(new OrderProductReturnCreatedEvent($orderProductReturn));
        }

        $updatedOrderData = [];
        $hasLoyaltyRewardPayment  = null;

        if(isset($data['payments'])) {
            $paymentRepository = app(PaymentRepository::class);

            $payments = collect($data['payments'])->map(function ($payment) use($order, $paymentRepository, $data, $referenceOrder) {
                $payment['receivedAmount'] = abs($payment['receivedAmount']);
                $payment['changedAmount'] = abs($payment['changedAmount']);
                $payment['due'] = abs($payment['due']);
                $payment['amount'] = abs($payment['amount']);

                $paymentData = array_merge($payment, [
                    'status' =>  Payment::STATUS_SUCCESS,
                    'cashFlow' =>  $order->amount > 0 ? Payment::CASH_FLOW_IN : Payment::CASH_FLOW_OUT,
                    'payType' =>  count($data['orderProducts']) ? Payment::PAY_TYPE_ORDER_EXCHANGE : null,
                    'paymentableType' => count($data['orderProducts']) ? Payment::PAYMENT_SOURCE_ORDER : Payment::PAYMENT_SOURCE_ORDER_PRODUCT_RETURN,
                    'paymentableId' => $order->id,
                    'receivedByUserId' => $order->createdByUserId,
                    'createdByUserId' => $order->createdByUserId,
                    'date' => Carbon::now(),
                ]);

                $paymentRepository->getModel()->create($paymentData);

                return $paymentData;
            });

            $updatedOrderData['paid'] = abs($order->paid) + $payments->sum('amount');
            $updatedOrderData['due'] = abs(round($order->amount, 2)) - round($updatedOrderData['paid'],2);
            $updatedOrderData['date'] =Carbon::now();
            $updatedOrderData['paymentStatus'] = Payment::paymentStatus($updatedOrderData['due'], $updatedOrderData['paid']);

            $hasLoyaltyRewardPayment = $payments->firstWhere('method', Payment::METHOD_LOYALTY_REWARD);
        }

        if($order->amount == 0) {
            $updatedOrderData['paid'] = 0;
            $updatedOrderData['due'] = 0;
            $updatedOrderData['paymentStatus'] = Payment::paymentStatus($updatedOrderData['due'], $updatedOrderData['paid']);
        }

        //TODO: add a validation on request for check points and amount for conversions
        if($hasLoyaltyRewardPayment instanceof Payment) {
            $pointsRedeemedInThisOrder =$hasLoyaltyRewardPayment->redeemedPoints;

            $clrData = [
                'customerId' => $order->customerId,
                'loyaltyableId' => $hasLoyaltyRewardPayment->id,
                'loyaltyableType' => CustomerLoyaltyReward::TYPE_PAYMENT,
                'action' => CustomerLoyaltyReward::ACTION_REDEEMED,
                'points' => $pointsRedeemedInThisOrder,
                'amount' => $hasLoyaltyRewardPayment->amount,
            ];

            app(CustomerLoyaltyRewardRepository::class)->save($clrData);
        }

        if (!empty($data['quotationId'])){
            $quotationRepo = app(QuotationRepository::class);
            $quotation = $quotationRepo->findOne($data['quotationId']);
            if ($quotation instanceof Quotation){
                $quotationRepo->update($quotation, ['status' => Quotation::STATUS_SOLD]);
            }
        }

        app(OrderLogRepository::class)->save([
            'createdByUserId' => $order->createdByUserId,
            'orderId' => $order->id,
            'comment' => 'order is just placed.',
            'status' => $order->status,
            'paymentStatus' => is_array($updatedOrderData) && isset($updatedOrderData['paymentStatus']) ? $updatedOrderData['paymentStatus'] : '',
            'deliveryStatus' => $order->status,
        ]);

        DB::commit();

        event(new OrderCreatedEvent($order, $updatedOrderData));

        return $order;
    }

    /**
     * @inheritDoc
     */
    public function update(\ArrayAccess $model, array $data): \ArrayAccess
    {
        DB::beginTransaction();

        $order = parent::update($model, $data);

        if(isset($data['orderProducts'])) {
            $orderProductRepository = app(OrderProductRepository::class);

            $orderProductRepository->model->where('orderId',$model->id)->delete();

            foreach ($data['orderProducts'] as $product) {
                $orderProduct['createdByUserId'] = $order->createdByUserId;
                $orderProduct['orderId'] = $order->id;
                $orderProduct['branchId'] = $order->branchId;
                $orderProduct['date'] = $order->date;
                $orderProduct['productId'] = $product['productId'];
                $orderProduct['unitPrice'] = $product['unitPrice'];
                $orderProduct['quantity'] = $product['quantity'];
                $orderProduct['tax'] = $product['tax'] ?? null;
                $orderProduct['size'] = $product['size'] ?? null;
                $orderProduct['color'] = $product['color'] ?? null;
                $orderProduct['status'] = $product['status'];
                $orderProduct['discount'] = $product['discount'];
                $orderProduct['amount'] = $product['amount'];
                $orderProductRepository->save($orderProduct);
            }
        }

        if($order->branch->type == Branch::TYPE_ECOMMERCE && $order->ecomInvoice && $order->referenceId) {
            event(new OrderUpdatingEvent($order, $this->generateEventOptionsForModel()));
        }

        DB::commit();

        return $order;
    }

    /**
     * shorten the search based on search criteria
     *
     * @param $searchCriteria
     * @return mixed
     */
    private function applyFilterInOrderSearch($searchCriteria)
    {
        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('invoice', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhereHas('customer', function($query) use ($searchCriteria){
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                          ->orWhere('email', 'like', '%' . $searchCriteria['query'] . '%')
                          ->orWhere('phone', 'like', '%' . $searchCriteria['query'] . '%');
                })
                ->orWhereHas('orderProducts.product', function ($query) use ($searchCriteria){
                    $query->where('name', 'like', '%'.$searchCriteria['query'].'%')
                        ->orWhere('barcode', 'like', '%'.$searchCriteria['query'].'%');
                })
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }

        if (isset($searchCriteria['id'])) {
            $searchCriteria['id'] = is_array($searchCriteria['id']) ? implode(",", array_unique($searchCriteria['id'])) : $searchCriteria['id'];
        }

        return $searchCriteria;
    }


    /**
     * @param array $searchCriteria
     * @param $withTrashed
     * @return array
     */
    public function salesManWiseOrder(array $searchCriteria = [], $withTrashed = false): array
    {
        $queryBuilder = $this->model->where('salePersonId', '!=', null);

        if (isset($searchCriteria['endDate'])) {
            $queryBuilder  =  $queryBuilder->whereDate('created_at', '<=', Carbon::parse($searchCriteria['endDate'])->endOfDay());
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder =  $queryBuilder->whereDate('created_at', '>=', Carbon::parse($searchCriteria['startDate'])->startOfDay());
            unset($searchCriteria['startDate']);
        }

        if (isset($searchCriteria['paymentStatusGroup'])){
            $allPaymentStatus = explode(',', $searchCriteria['paymentStatusGroup']);
            $queryBuilder = $queryBuilder->whereIn('paymentStatus', $allPaymentStatus);
            unset($searchCriteria['paymentStatusGroup']);
        }

        if (isset($searchCriteria['paymentMethod'])){
            $queryBuilder = $queryBuilder->orWhereHas('payments', function($query) use ($searchCriteria){
                $query->where('method', '=', $searchCriteria['paymentMethod']);
            });
            unset($searchCriteria['paymentMethod']);
        }

        $withSummary = !empty($searchCriteria['withSummary']);
        unset($searchCriteria['withSummary']);

        $searchCriteria = $this->applyFilterInOrderSearch($searchCriteria);

        $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $queryBuilder->orderBy($orderBy, $orderDirection);

        $searchCriteria['eagerLoad'] = [
            'order.orderProducts' => 'orderProducts',
            'order.branch' => 'branch',
            'order.payments' => 'payments',
            'order.invoiceImage' => 'invoiceImage',
            'order.customer' => 'customer',
            'order.salePerson' => 'salePerson',
            'order.createdByUser' => 'createdByUser',
            'order.orderProductReturns' => 'orderProductReturns',
            'op.product' => 'orderProducts.product',
            'op.discount' => 'orderProducts.getDiscount',
            'op.tax' => 'orderProducts.getTax',
            'order.coupon' => 'coupon'
        ];

        $queryBuilder = $this->applyEagerLoad($queryBuilder, $searchCriteria);

        $queryBuilder->withSum('orderProductReturns', 'returnAmount', 'profitAmount', 'discountAmount');

        $summary = [];
        if ($withSummary) {
            $summary['totalSalesAmount'] = $queryBuilder->sum('amount');
            $summary['totalPaid'] = $queryBuilder->sum('paid');
            $summary['totalDue'] = $queryBuilder->sum('due');
            $summary['totalProfit'] = round($queryBuilder->sum('profitAmount'),2);
            $summary['totalDiscount'] = round($queryBuilder->sum('discount'),2);
            $summary['totalNet'] = round($queryBuilder->sum('grossProfit'),2);

            $queryBuilderAllForTotalReturn = $queryBuilder->get();
            $summary['totalReturnAmount'] = round($queryBuilderAllForTotalReturn->sum('order_product_returns_sum_return_amount'),2);
            $summary['totalNetPaidAmount'] = round(($queryBuilder->sum('paid') - $queryBuilderAllForTotalReturn->sum('order_product_returns_sum_return_amount')),2);
            $summary['totalNetGrossProfit'] = round($queryBuilder->sum('grossProfit') - ($queryBuilderAllForTotalReturn->sum('order_product_returns_sum_profit_amount') - $queryBuilderAllForTotalReturn->sum('order_product_returns_sum_discount_amount')),2);
            $summary['totalNetTotalAmount'] = round($queryBuilder->sum('amount') - $queryBuilderAllForTotalReturn->sum('order_product_returns_sum_return_amount'), 2);
        }

        if (empty($searchCriteria['withoutPagination'])) {
            $page = !empty($searchCriteria['page']) ? (int)$searchCriteria['page'] : 1;
            $orders = $queryBuilder->paginate($limit, ['*'], 'page', $page);
        } else {
            $orders= $queryBuilder->get();
        }

        if(!empty($searchCriteria['withoutPagination'])) {
            $pageWiseSummary = $summary;
        } else {
            $pageWiseSummary['totalSalesAmount'] = round($orders->sum('amount'),2);
            $pageWiseSummary['totalPaid'] = round($orders->sum('paid'),2);
            $pageWiseSummary['totalDue'] = round($orders->sum('due'),2);
            $pageWiseSummary['totalProfit'] = round($orders->sum('profitAmount'),2);
            $pageWiseSummary['totalDiscount'] = round($orders->sum('discount'),2);
            $pageWiseSummary['totalNet'] = round($orders->sum('grossProfit'),2);

            $pageWiseSummary['totalReturnAmount'] = round($orders->sum('order_product_returns_sum_return_amount'),2);
            $pageWiseSummary['totalNetPaidAmount'] = round(($orders->sum('paid') - $orders->sum('order_product_returns_sum_return_amount')),2);
            $pageWiseSummary['totalNetGrossProfit'] = round($orders->sum('grossProfit') - ($orders->sum('order_product_returns_sum_profit_amount') - $orders->sum('order_product_returns_sum_discount_amount')),2);
            $pageWiseSummary['totalNetTotalAmount'] = round($orders->sum('amount') - $orders->sum('order_product_returns_sum_return_amount'), 2);
        }

        $result = [
            'orders' => $orders,
            'pageWiseSummary' => $pageWiseSummary,
        ];

        if ($withSummary) {
            $result['summary'] = $summary;
        }

        return $result;
    }


    /**
     * @param array $data
     * @param int $orderId
     * @return mixed
     */
    public function changeStatus(array $data, int $orderId)
    {
        DB::beginTransaction();

        $order = $this->model->where('id', $orderId)->first();

        $updatedOrderData = [];
        $updatedOrderData['comment'] = $data['comment'];

        if(isset($data['status']) && $data['status'] == Order::STATUS_DELIVERED && isset($data['payments'])) {
            $paymentRepository = app(PaymentRepository::class);

            $payments = collect($data['payments'])->map(function ($payment) use($order, $paymentRepository) {
                $paymentData = array_merge($payment, [
                    'status' =>  Payment::STATUS_SUCCESS,
                    'cashFlow' =>  Payment::CASH_FLOW_IN,
                    'payType' =>  Payment::PAY_TYPE_ORDER,
                    'paymentableType' => Payment::PAYMENT_SOURCE_ORDER,
                    'paymentableId' => $order->id,
                    'receivedByUserId' => $order->createdByUserId,
                    'createdByUserId' => $order->createdByUserId,
                    'date' => Carbon::now(),
                ]);

                $paymentRepository->getModel()->create($paymentData);

                return $paymentData;
            });

            $updatedOrderData['paid'] = $order->paid + $payments->sum('amount');
            $updatedOrderData['due'] = round($order->amount, 2) - round($updatedOrderData['paid'],2);
            $updatedOrderData['date'] = Carbon::now();
            $updatedOrderData['paymentStatus'] = Payment::paymentStatus($updatedOrderData['due'], $updatedOrderData['paid']);
            $updatedOrderData['status'] = Order::STATUS_DELIVERED;
        }

        if (isset($data['status']) && $data['status'] == Order::STATUS_CANCELLED){

            $orderProducts = $order->orderProducts;
            foreach ($orderProducts as $orderProduct){

                $status = OrderProduct::STATUS_CANCELLED;

                $orderProductRepository = app(OrderProductRepository::class);
                $orderProductRepository->update($orderProduct, ['status' => $status]);

                $stock = $orderProduct->stock;

                $profitAmount = $stock->unitProfit * $orderProduct->quantity;
                $discountAmount = ($orderProduct->unitPrice - $orderProduct->discountedUnitPrice) * $orderProduct->quantity;

                if($stock instanceof Stock) {
                    $prevQuantity = $stock->quantity;
                    $stockData['quantity'] = $stock->quantity + $orderProduct->quantity;

                    $stockRepository = app(StockRepository::class);
                    $updateStock = $stockRepository->update($stock, $stockData);

                    $stockLogRepository = app(StockLogRepository::class);
                    $stockLogRepository->save([
                        'stockId' => $stock->id,
                        'productId' => $orderProduct->productId,
                        'resourceId' => $orderProduct->id,
                        'type' => StockLog::TYPE_ORDER_PRODUCT_CANCELLED,
                        'prevQuantity' => $prevQuantity,
                        'newQuantity' => $orderProduct->quantity,
                        'quantity' => $updateStock->quantity,
                        'profitAmount' => $profitAmount,
                        'discountAmount' => $discountAmount,
                        'date' => $orderProduct->date ?? now(),
                    ]);
                }
            }

            $updatedOrderData['status'] = Order::STATUS_CANCELLED;
        }

        if($order->amount == 0) {
            $updatedOrderData['paid'] = 0;
            $updatedOrderData['due'] = 0;
            $updatedOrderData['paymentStatus'] = Payment::paymentStatus($updatedOrderData['due'], $updatedOrderData['paid']);
        }

        parent::update($order, $updatedOrderData);

        DB::commit();

        event(new OrderCreatedEvent($order, $updatedOrderData));

        if($order->branch->type == Branch::TYPE_ECOMMERCE && $order->ecomInvoice && $order->referenceId) {
            event(new OrderUpdatingEvent($order, $this->generateEventOptionsForModel()));
        }

        return $order;
    }

    /**
     * @param array $data
     * @param int $orderId
     * @return mixed
     */
    public function changeOrderStatus(array $data, int $orderId)
    {
        $order = $this->model->where('id', $orderId)->first();

        parent::update($order, ['status' => $data['status']]);

        return $order;
    }
}
