<?php


namespace App\Repositories;

use App\Events\StockTransfer\StockTransferCreatedEvent;
use App\Repositories\Contracts\StockTransferProductRepository;
use Carbon\Carbon;


class EloquentStockTransferProductRepository extends EloquentBaseRepository implements StockTransferProductRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        $queryBuilder = $this->model;

        if (isset($searchCriteria['endDate'])) {
            $queryBuilder  =  $queryBuilder->whereDate('date', '<=', Carbon::parse($searchCriteria['endDate'])->toDateString());
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder =  $queryBuilder->whereDate('date', '>=', Carbon::parse($searchCriteria['startDate'])->toDateString());
            unset($searchCriteria['startDate']);
        }

        if (isset($searchCriteria['branchId'])) {
            $queryBuilder =  $queryBuilder->where('fromBranchId', $searchCriteria['branchId'])->orWhere('toBranchId', $searchCriteria['branchId']);
            unset($searchCriteria['branchId']);
        }

        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('status', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('sku', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhereHas('product', function($query) use ($searchCriteria){
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%');
                })
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

        if (empty($searchCriteria['withoutPagination'])) {
            return $queryBuilder->paginate($limit);
        } else {
            return $queryBuilder->get();
        }
    }
}
