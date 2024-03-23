<?php


namespace App\Repositories;


use App\Repositories\Contracts\DepartmentRepository;

class EloquentDepartmentRepository extends EloquentBaseRepository implements DepartmentRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model
                ->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }

        return parent::findBy($searchCriteria);
    }

    /**
     * create or get company by name
     *
     * @param string $departmentName
     * @return \ArrayAccess|null
     */
    public function createOrGetDepartmentByName($departmentName): ?\ArrayAccess
    {
        $department = $this->findOneBy(['name' => $departmentName]);

        if (!$department) {
            $department = $this->save(['name' => $departmentName]);
        }

        return $department;
    }
}
