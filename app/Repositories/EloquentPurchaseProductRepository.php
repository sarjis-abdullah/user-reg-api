<?php


namespace App\Repositories;


use App\Events\PurchaseProduct\PurchaseProductCreatedEvent;
use App\Models\ProductVariation;
use App\Models\Purchase;
use App\Repositories\Contracts\ProductRepository;
use App\Repositories\Contracts\PurchaseProductRepository;
use Carbon\Carbon;

class EloquentPurchaseProductRepository extends EloquentBaseRepository implements PurchaseProductRepository
{
    /**
     * @inheritDoc
     */
    public function save(array $data): \ArrayAccess
    {
        $product = app(ProductRepository::class)->findOne($data['productId']);

        if (!isset($data['sku'])) {
            if (isset($data['productVariationId'])) {
                $productVariation = ProductVariation::find($data['productVariationId']);
                $extendSku = implode('-', [
                    $data['sellingPrice'],
                    $productVariation->size ?? '',
                    $productVariation->color ?? '',
                    $productVariation->material ?? ''
                ]);
            } else {
                $extendSku = $data['sellingPrice'];
            }

            $data['sku'] = Purchase::generateSku($product->name, $data['productId'], rtrim($extendSku, '-'));
        }

        $stockSerialIds = [];
        if(isset($data['stockSerialIds'])) {
            $stockSerialIds = $data['stockSerialIds'];

            if($data['status'] != Purchase::STATUS_RECEIVED) {
                $data['serialIds'] = $data['stockSerialIds'];
            }
        }

        $purchaseProduct = parent::save($data);

        if($purchaseProduct->purchase->status == Purchase::STATUS_RECEIVED) {
            event(new PurchaseProductCreatedEvent($purchaseProduct, $stockSerialIds));
        }

        return $purchaseProduct;
    }
    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        $queryBuilder = $this->model;

        if (isset($searchCriteria['endDate'])) {
            $queryBuilder  =  $queryBuilder->whereDate('created_at', '<=', Carbon::parse($searchCriteria['endDate']));
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder =  $queryBuilder->whereDate('created_at', '>=', Carbon::parse($searchCriteria['startDate']));
            unset($searchCriteria['startDate']);
        }

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
}
