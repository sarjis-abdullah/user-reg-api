<?php

namespace App\Repositories;

use App\Repositories\Contracts\ProductStockSerialRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EloquentProductStockSerialRepository extends EloquentBaseRepository implements ProductStockSerialRepository
{
    /**
     * @param array $searchCriteria
     * @param $withTrashed
     * @return LengthAwarePaginator|Builder|Collection
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        $queryBuilder = $this->model->newQuery();

        if (isset($searchCriteria['query'])){
            $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria){
                $query->where('productStockSerialId', 'LIKE', "%{$searchCriteria['query']}%")
                    ->orWhere('status', 'LIKE', "%{$searchCriteria['query']}%")
                    ->orWhereHas('product', function ($query) use ($searchCriteria){
                        $query->where('name', 'LIKE', "%{$searchCriteria['query']}%");
                    })
                    ->orWhereHas('stock', function ($query) use ($searchCriteria){
                        $query->where('sku', 'LIKE', "%{$searchCriteria['query']}%");
                    });
            });
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

    /**
     * @param array $data
     * @return \ArrayAccess
     */
    public function save(array $data): \ArrayAccess
    {
        return parent::save($data);
    }

    /**
     * @param \ArrayAccess $model
     * @param array $data
     * @return \ArrayAccess
     */
    public function update(\ArrayAccess $model, array $data): \ArrayAccess
    {
        return parent::update($model, $data);
    }
}
