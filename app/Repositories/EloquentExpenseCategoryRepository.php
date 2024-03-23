<?php


namespace App\Repositories;


use App\Repositories\Contracts\ExpenseCategoryRepository;

class EloquentExpenseCategoryRepository extends EloquentBaseRepository implements ExpenseCategoryRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhereHas('branch', function($query) use ($searchCriteria){
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%');
                })
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }
        return $this->findByWithDateRanges($searchCriteria, $withTrashed, true);
    }
}
