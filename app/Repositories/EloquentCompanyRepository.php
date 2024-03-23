<?php


namespace App\Repositories;


use App\Repositories\Contracts\CompanyRepository;

class EloquentCompanyRepository extends EloquentBaseRepository implements CompanyRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('email', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('address', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('phone', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('type', 'like', '%' . $searchCriteria['query'] . '%')
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }

        return parent::findBy($searchCriteria);
    }

    /**
     * create or get company by name
     *
     * @param string $companyName
     * @return \ArrayAccess|null
     */
    public function createOrGetCompanyByName($companyName)
    {
        $company = $this->findOneBy(['name' => $companyName]);

        if (!$company) {
            $company = $this->save(['name' => $companyName, 'email' => 'NA', 'address' => 'NA', 'phone' => 'NA', 'type' => 'NA']);
        }

        return $company;
    }
}
