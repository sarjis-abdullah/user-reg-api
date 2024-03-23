<?php


namespace App\Repositories;


use App\Models\Branch;
use App\Repositories\Contracts\BranchRepository;
use Carbon\Carbon;

class EloquentBranchRepository extends EloquentBaseRepository implements BranchRepository
{
    /*
    * @inheritdoc
    */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        $queryBuilder = $this->model;

        if (isset($searchCriteria['endDate'])) {
            $queryBuilder = $queryBuilder->whereDate('created_at', '<=', Carbon::parse($searchCriteria['endDate']));
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder = $queryBuilder->whereDate('created_at', '>=', Carbon::parse($searchCriteria['startDate']));
            unset($searchCriteria['startDate']);
        }

        $searchCriteria['type'] = $searchCriteria['type'] ?? Branch::TYPE_SELF . ',' . Branch::TYPE_FRANCHISE;

        if(isset($searchCriteria['query'])) {
            $queryBuilder = $queryBuilder->where('name', 'like', $searchCriteria['query'] . '%')
                ->orWhere('email', 'like', $searchCriteria['query'] . '%')
                ->orWhere('phone', 'like', $searchCriteria['query'] . '%');
            unset($searchCriteria['query']);
        }

        $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $queryBuilder->orderBy($orderBy, $orderDirection);

        if ($withTrashed) {
            $queryBuilder->withTrashed();
        }

        if (empty($searchCriteria['withoutPagination'])) {
            return $queryBuilder->paginate($limit);
        } else {
            return $queryBuilder->get();
        }
    }
}
