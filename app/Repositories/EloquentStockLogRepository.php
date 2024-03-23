<?php


namespace App\Repositories;


use App\Repositories\Contracts\StockLogRepository;
use Carbon\Carbon;

class EloquentStockLogRepository extends EloquentBaseRepository implements StockLogRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        $queryBuilder = $this->model;

        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $queryBuilder->where('type', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhereHas('createdByUser', function($query) use ($searchCriteria){
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%');
                })
                ->orWhereHas('product', function($query) use ($searchCriteria){
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%');
                })
                ->orWhereHas('product', function($query) use ($searchCriteria){
                    $query->where('barcode', 'like', '%' . $searchCriteria['query'] . '%');
                })
                ->orWhereHas('stock', function($query) use ($searchCriteria){
                    $query->where('sku', 'like', '%' . $searchCriteria['query'] . '%');
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

        if (isset($searchCriteria['createdAt'])) {
            $queryBuilder =  $queryBuilder->whereDate('created_at', '=', Carbon::parse($searchCriteria['createdAt']));
            unset($searchCriteria['createdAt']);
        }
        if (isset($searchCriteria['branchId'])) {
            $queryBuilder = $queryBuilder->whereHas('stock', function ($query) use ($searchCriteria){
                $query->where('branchId', $searchCriteria['branchId']);
            });
            unset($searchCriteria['branchId']);
        }

        if (isset($searchCriteria['id'])) {
            $searchCriteria['id'] = is_array($searchCriteria['id']) ? implode(",", array_unique($searchCriteria['id'])) : $searchCriteria['id'];
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
