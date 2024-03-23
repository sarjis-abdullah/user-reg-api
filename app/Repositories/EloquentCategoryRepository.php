<?php


namespace App\Repositories;


use App\Events\Woocommerce\CategorySavingEvent;
use App\Models\Branch;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepository;
use Carbon\Carbon;

class EloquentCategoryRepository extends EloquentBaseRepository implements CategoryRepository
{
    public function save(array $data): \ArrayAccess
    {
        $category = parent::save($data);

        if (Branch::where('type', Branch::TYPE_ECOMMERCE)->first()->exists()) {
            event(new CategorySavingEvent('saved', $category));
        }

        return $category;
    }

    public function update(\ArrayAccess $model, array $data): \ArrayAccess
    {
        $category = parent::update($model, $data);

        if (Branch::where('type', Branch::TYPE_ECOMMERCE)->first()->exists()) {
            event(new CategorySavingEvent('updated', $category));
        }

        return $category;
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

        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('code', 'like', '%' . $searchCriteria['query'] . '%')
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

    /**
     * create or get category by name
     *
     * @param string $categoryName
     * @return \ArrayAccess|null
     */
    public function createOrGetCategoryByName($categoryName)
    {
        $category = $this->findOneBy(['name' => $categoryName]);
        if (!$category) {
            $category = $this->save(['name' => $categoryName]);
        }

        return $category;
    }

}
