<?php


namespace App\Repositories;

use App\Repositories\Contracts\DeliveryAgencyRepository;

class EloquentDeliveryAgencyRepository extends EloquentBaseRepository implements DeliveryAgencyRepository
{

    /**
     * @param array $searchCriteria
     * @param $withTrashed
     * @return mixed
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('email', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('phone', 'like', '%' . $searchCriteria['query'] . '%')
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }
        if (isset($searchCriteria['id'])) {
            $searchCriteria['id'] = is_array($searchCriteria['id']) ? implode(",", array_unique($searchCriteria['id'])) : $searchCriteria['id'];
        }

        return parent::findBy($searchCriteria);
    }
}
