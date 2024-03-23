<?php

namespace App\Repositories;

use App\Events\PurchaseProductReturn\PurchaseProductReturnCreatedEvent;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\PurchaseProduct;
use App\Repositories\Contracts\PaymentRepository;
use App\Repositories\Contracts\ProductRepository;
use App\Repositories\Contracts\PurchaseProductRepository;
use App\Repositories\Contracts\PurchaseProductReturnRepository;
use App\Repositories\Contracts\PurchaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EloquentPurchaseProductReturnRepository extends EloquentBaseRepository implements PurchaseProductReturnRepository
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
            $totalReturnAmount = 0;
            foreach ($products as $product) {
                $data['purchaseProductId'] = $product['purchaseProductId'];
                $data['quantity'] = $product['quantity'];
                $data['returnAmount'] = $product['returnAmount'];
                $data['date'] = $product['date'] ?? Carbon::now();

                $purchaseProductReturn = parent::save($data);

                self::saveIntoPayments($data, $purchaseProductReturn);

                $ids[] = $purchaseProductReturn->id;

                event(new PurchaseProductReturnCreatedEvent($purchaseProductReturn));

                $totalReturnAmount += $product['returnAmount'];
            }

            $this->setDueUponGettableDueAmount($data);

            $this->updatePurchaseReturnValue($data, $totalReturnAmount);

            return $this->model->whereIn('purchaseProductId', $ids)->get();
        }

        $this->setDueUponGettableDueAmount($data);

        $ppr = parent::save($data);

        self::saveIntoPayments($data, $ppr);

        event(new PurchaseProductReturnCreatedEvent($ppr));

        return $ppr;
    }

    /**
     * @param array $data
     * @return void
     */
    public function setDueUponGettableDueAmount(array $data)
    {
        if(isset($data['gettableDueAmount'])) {
            $purchaseRepo = app(PurchaseRepository::class);

            $purchase = $purchaseRepo->findOne($data['purchaseId']);

            if($purchase instanceof Purchase) {
                $purchaseRepo->update($purchase, ['due' => 0, 'gettableDueAmount' => $data['gettableDueAmount']]);
            }

            unset($data['gettableDueAmount']);
        }
    }

    public function updatePurchaseReturnValue($data, $totalReturnAmount)
    {

            $purchaseRepo = app(PurchaseRepository::class);
            $purchase = $purchaseRepo->findOne($data['purchaseId']);
            $dueAmount = 0;

            if (!isset($data['gettableDueAmount']) && $purchase->totalAmount != $purchase->paidAmount){

                $dueAmount = ($purchase->totalAmount - $purchase->paid ) - ($purchase->returnedAmount + $totalReturnAmount);

            }
            if ($purchase instanceof Purchase){

                $purchaseRepo->update($purchase, [
                    'returnedAmount' => $purchase->returnedAmount + $totalReturnAmount,
                    'due' => $dueAmount
                ]);
            }
    }
    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        $queryBuilder = $this->model;

        if (isset($searchCriteria['query'])) {
            $productRepository = app(ProductRepository::class);
            $products = $productRepository->model->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                ->pluck('id')->toArray();

            $searchCriteria['id'] = $this->model->where('returnAmount', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhereHas('branch', function($query) use ($searchCriteria){
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%');
                })
                ->pluck('id')->toArray();

            if (isset($products))
                $searchCriteria['id'] = $this->model->whereHas('purchaseProduct', function($query) use ($products){
                    $query->where('productId', '=', $products['id']);
                })->pluck('id')->toArray();

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

        if ($withSummary) {
            unset($searchCriteria['withSummary']);
            $summary['totalReturnAmount'] = round($queryBuilder->sum('returnAmount'),2);
            $summary['totalReturnQuantity'] = round($queryBuilder->sum('quantity'),2);
        }

        if (empty($searchCriteria['withoutPagination'])) {
            $returnPurchases = $queryBuilder->paginate($limit);
        } else {
            $returnPurchases= $queryBuilder->get();
        }

        return $withSummary ? ['returnPurchases' => $returnPurchases, 'summary' => $summary] : $returnPurchases;
    }

    /**
     * @param $data
     * @param $opr
     * @return void
     */
    public function saveIntoPayments($data, $ppr)
    {
        if(isset($data['payment'])) {
            $paymentData = $data['payment'];
            $paymentData['status'] = Payment::STATUS_SUCCESS;
            $paymentData['cashFlow'] = Payment::CASH_FLOW_IN;
            $paymentData['paymentableId'] = $ppr->id;
            $paymentData['paymentableType'] = Payment::PAYMENT_SOURCE_PURCHASE_PRODUCT_RETURN;
            $paymentData['receivedByUserId'] = $ppr->createdByUserId;
            $paymentData['date'] = Carbon::now();
            $paymentData['amount'] = $ppr->returnAmount;
            $paymentData['method'] = Payment::METHOD_CASH;

            $paymentRepository = app(PaymentRepository::class);
            $paymentRepository->save($paymentData);
        }
    }
}
