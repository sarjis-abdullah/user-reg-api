<?php

namespace App\Repositories;

use App\Events\OrderProductReturn\OrderProductReturnCreatedEvent;
use App\Models\Payment;
use App\Repositories\Contracts\OrderProductRepository;
use App\Repositories\Contracts\OrderProductReturnRepository;
use App\Repositories\Contracts\PaymentRepository;
use Carbon\Carbon;

class EloquentOrderProductReturnRepository extends EloquentBaseRepository implements OrderProductReturnRepository
{
    /**
     * @inheritDoc
     */
    public function save(array $data): \ArrayAccess
    {
        if(isset($data['products'])) {
            $products = $data['products'];
            unset($data['products']);
            $ids=[];
            foreach ($products as $product) {

                $orderProductRepository = app(OrderProductRepository::class);
                $orderProductModel = $orderProductRepository->getModel();
                $orderProduct = $orderProductModel->where('id', $product['orderProductId'])->first(['id', 'productId', 'stockId']);

                $data['orderProductId'] = $product['orderProductId'];
                $data['productId'] = $orderProduct ? $orderProduct->productId : null;
                $data['stockId'] = $orderProduct ? $orderProduct->stockId : null;
                $data['quantity'] = $product['quantity'];
                $data['returnAmount'] = $product['returnAmount'];

                $orderProductReturn = parent::save($data);
                self::saveIntoPayments($data, $orderProductReturn);

                $ids[] = $orderProductReturn->id;

                event(new OrderProductReturnCreatedEvent($orderProductReturn));
            }

            return $this->model->whereIn('id', $ids)->get();
        }


        $opr = parent::save($data);
        self::saveIntoPayments($data, $opr);

        event(new OrderProductReturnCreatedEvent($opr));

        return $opr;
    }

    /**
     * @param array $data
     * @return \ArrayAccess
     */
    public function saveReturnAbleProduct(array $data): \ArrayAccess
    {
        $opr = parent::save($data);

        return $opr;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        $queryBuilder = $this->model;

        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('comment', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('returnAmount', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhereHas('product', function($query) use ($searchCriteria){
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%');
                })
                ->orWhereHas('branch', function($query) use ($searchCriteria){
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%');
                })
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }
        if (isset($searchCriteria['endDate'])) {
            $queryBuilder  =  $queryBuilder->whereDate('created_at', '<=', Carbon::parse($searchCriteria['endDate']));
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder =  $queryBuilder->whereDate('created_at', '>=', Carbon::parse($searchCriteria['startDate']));
            unset($searchCriteria['startDate']);
        }
        if (isset($searchCriteria['id'])) {
            $searchCriteria['id'] = is_array($searchCriteria['id']) ? implode(",", array_unique($searchCriteria['id'])) : $searchCriteria['id'];
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

        $summary = [];



        if (empty($searchCriteria['withoutPagination'])) {
            $returnOrders = $queryBuilder->paginate($limit);
        } else {
            $returnOrders = $queryBuilder->get();
        }

        if ($withSummary) {
            unset($searchCriteria['withSummary']);
            $summary['totalReturnAmount'] = round($queryBuilder->sum('returnAmount'),2);
            $summary['totalReturnQuantity'] = round($queryBuilder->sum('quantity'),2);
        }

        return $withSummary ? ['returnOrders' => $returnOrders, 'summary' => $summary] : $returnOrders;

    }

    public function saveIntoPayments($data, $opr)
    {
        if(isset($data['payment'])) {
            $paymentData = $data['payment'];
            $paymentData['status'] = Payment::STATUS_SUCCESS;
            $paymentData['cashFlow'] = Payment::CASH_FLOW_OUT;
            $paymentData['paymentableId'] = $opr->id;
            $paymentData['paymentableType'] = Payment::PAYMENT_SOURCE_ORDER_PRODUCT_RETURN;
            $paymentData['receivedByUserId'] = $opr->createdByUserId;
            $paymentData['date'] = Carbon::now();
            $paymentData['amount'] = $opr->returnAmount;
            $paymentData['method'] = Payment::METHOD_CASH;

            $paymentRepository = app(PaymentRepository::class);
            $paymentRepository->save($paymentData);
        }
    }
}
