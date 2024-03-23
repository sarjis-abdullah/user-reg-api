<?php

namespace App\Repositories;

use App\Repositories\Contracts\DiscountRepository;
use Carbon\Carbon;

class EloquentDiscountRepository extends EloquentBaseRepository implements DiscountRepository
{

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('title', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('type', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('amount', 'like', '%' . $searchCriteria['query'] . '%')
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }
        return $this->findByWithDateRanges($searchCriteria, $withTrashed, true);
    }
}
