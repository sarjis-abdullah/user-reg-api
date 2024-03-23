<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductReturn;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\PurchaseProductReturn;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\PaymentRepository;
use App\Repositories\Contracts\PurchaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EloquentPaymentRepository extends EloquentBaseRepository implements PaymentRepository
{
    /**
     * @inheritDoc
     */
    public function save(array $data): \ArrayAccess
    {
        $data['date'] = Carbon::now();

        if(isset($data['paid'])) {
            unset($data['paid']);
        }

        if ($data['paymentableType'] == Payment::PAYMENT_SOURCE_ORDER_DUE){
            $data['payType'] = Payment::PAY_TYPE_ORDER_DUE;
        }elseif ($data['paymentableType'] == Payment::PAYMENT_SOURCE_PURCHASE_DUE){
            $data['payType'] = Payment::PAY_TYPE_PURCHASE_DUE;
        }

        $payment = parent::save($data);

        if ($payment->paymentableType === Payment::PAYMENT_SOURCE_ORDER || $payment->paymentableType === Payment::PAYMENT_SOURCE_ORDER_DUE) {
            $orderRepository = app(OrderRepository::class);

            $order = $orderRepository->findOne($payment->paymentableId);

            if($order instanceof Order){
                $orderData['paid'] = $order->paid + $payment->amount;
                $orderData['due'] = round($order->amount, 2)- round($orderData['paid'],2);
                $orderData['date'] =Carbon::now();
                $orderData['paymentStatus'] = Payment::paymentStatus($orderData['due'], $orderData['paid']);

                $orderRepository->update($order, $orderData);
            }
        } else if ($payment->paymentableType === Payment::PAYMENT_SOURCE_PURCHASE || $payment->paymentableType === Payment::PAYMENT_SOURCE_PURCHASE_DUE) {
            $purchaseRepository = app(PurchaseRepository::class);

            $purchase = $purchaseRepository->findOne($payment->paymentableId);

            if($purchase instanceof Purchase){
                $purchaseData['paid'] = $purchase->paid + $payment->amount;
                $purchaseData['due'] = $purchase->totalAmount - $purchaseData['paid'];
                $purchaseData['date'] =Carbon::now();
                $purchaseData['paymentStatus'] = Payment::paymentStatus($purchaseData['due'], $purchaseData['paid']);

                $purchaseRepository->update($purchase, $purchaseData);
            }
        }

        return $payment;
    }

    /**
     * @param array $data
     * @return \ArrayAccess
     */
    public function saveOnlyPayment(array $data): \ArrayAccess
    {
        return parent::save($data);
    }

    /**
     * @param array $searchCriteria
     * @return array
     */
    public function paymentSummary(array $searchCriteria = []): array
    {
        $queryBuilder = $this->model->where('amount', '>', 0);

        if (isset($searchCriteria['endDate'])) {
            $queryBuilder  =  $queryBuilder->whereDate('created_at', '<=', Carbon::parse($searchCriteria['endDate']));
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder =  $queryBuilder->whereDate('created_at', '>=', Carbon::parse($searchCriteria['startDate']));
            unset($searchCriteria['startDate']);
        }

        if (isset($searchCriteria['paymentType'])) {
            $queryBuilder =  $queryBuilder->when($searchCriteria['paymentType'] == Payment::PAYMENT_TYPE_CREDIT, function ($query){
                $query->where('cashFlow', Payment::CASH_FLOW_IN);
            });

            $queryBuilder =  $queryBuilder->when($searchCriteria['paymentType'] == Payment::PAYMENT_TYPE_DEBIT, function ($query){
                $query->where('cashFlow', Payment::CASH_FLOW_OUT);
            });

            unset($searchCriteria['paymentType']);
        }

        if (isset($searchCriteria['paymentSource'])) {
            $queryBuilder =  $queryBuilder->when($searchCriteria['paymentSource'] == Payment::PAYMENT_SOURCE_ORDER, function ($query){
                $query->where('paymentableType', Order::class)->where('payType', Payment::PAY_TYPE_ORDER);
            });

            $queryBuilder =  $queryBuilder->when($searchCriteria['paymentSource'] == Payment::PAYMENT_SOURCE_PURCHASE, function ($query){
                $query->where('paymentableType', Purchase::class)->where('payType', Payment::PAY_TYPE_PURCHASE);
            });

            $queryBuilder =  $queryBuilder->when($searchCriteria['paymentSource'] == Payment::PAYMENT_SOURCE_ORDER_PRODUCT_RETURN, function ($query){
                $query->where('paymentableType', OrderProductReturn::class);
            });

            $queryBuilder =  $queryBuilder->when($searchCriteria['paymentSource'] == Payment::PAYMENT_SOURCE_PURCHASE_PRODUCT_RETURN, function ($query){
                $query->where('paymentableType', PurchaseProductReturn::class);
            });

            $queryBuilder =  $queryBuilder->when($searchCriteria['paymentSource'] == Payment::PAYMENT_SOURCE_INCOME, function ($query){
                $query->where('paymentableType', Income::class);
            });

            $queryBuilder =  $queryBuilder->when($searchCriteria['paymentSource'] == Payment::PAYMENT_SOURCE_EXPENSE, function ($query){
                $query->where('paymentableType', Expense::class);
            });

            $queryBuilder =  $queryBuilder->when($searchCriteria['paymentSource'] == Payment::PAYMENT_SOURCE_ORDER_DUE, function ($query){
                $query->where('paymentableType', Order::class)->where('payType', Payment::PAY_TYPE_ORDER_DUE);
            });

            $queryBuilder =  $queryBuilder->when($searchCriteria['paymentSource'] == Payment::PAYMENT_SOURCE_PURCHASE_DUE, function ($query){
                $query->where('paymentableType', Purchase::class)->where('payType', Payment::PAY_TYPE_PURCHASE_DUE);
            });

            unset($searchCriteria['paymentSource']);
        }

        if (isset($searchCriteria['branchId'])){

            /*
             * This code will be run one time. it's for update payment mode in staging server.
             */
            $invalidPaymentType = $this->model->where('paymentableType', '=', 'Class')->get();
            if ($invalidPaymentType->count()){
                foreach ($invalidPaymentType as $payment){
                    if ($payment->payType === Payment::PAYMENT_SOURCE_ORDER){
                        $paymentType = Order::class;
                    }elseif($payment->payType === Payment::PAYMENT_SOURCE_PURCHASE){
                        $paymentType = Purchase::class;
                    }elseif($payment->payType === Payment::PAYMENT_SOURCE_INCOME){
                        $paymentType = Income::class;
                    }elseif($payment->payType === Payment::PAYMENT_SOURCE_EXPENSE){
                        $paymentType = Expense::class;
                    }elseif($payment->payType === Payment::PAYMENT_SOURCE_ORDER_PRODUCT_RETURN){
                        $paymentType = OrderProductReturn::class;
                    }elseif($payment->payType === Payment::PAYMENT_SOURCE_PURCHASE_PRODUCT_RETURN){
                        $paymentType = PurchaseProductReturn::class;
                    }
                    DB::table('payments')
                        ->where('id', $payment->id)
                        ->update([
                        'paymentableType' => $paymentType
                    ]);
                }
            }

            $queryBuilder->whereHas('paymentable', fn($query) => $query->where('branchId', $searchCriteria['branchId']));
            unset($searchCriteria['branchId']);
        }

        if (isset($searchCriteria['method'])){
            $queryBuilder = $queryBuilder->where('method', $searchCriteria['method']);
            unset($searchCriteria['method']);
        }

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $queryBuilder = $queryBuilder->orderBy($orderBy, $orderDirection);

        $summary = [];

        if (isset($searchCriteria['withSummary'])){
            $queryForAll = $queryBuilder->get();

            $debit = $queryForAll->where('cashFlow', Payment::CASH_FLOW_OUT)->sum('amount');
            $credit = $queryForAll->where('cashFlow', Payment::CASH_FLOW_IN)->sum('amount');

            $summary = [
                'totalDebit' => $debit,
                'totalCredit' => $credit,
                'balance' => ($credit - $debit),
            ];
        }

        if (empty($searchCriteria['withoutPagination'])) {
            $result = $queryBuilder->paginate($limit);
        } else {
            $result = $queryBuilder->get();
        }

        return ['result' => $result, 'summary' => $summary];
    }
}
