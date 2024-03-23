<?php


namespace App\Repositories;


use App\Repositories\Contracts\SubDepartmentRepository;

class EloquentSubDepartmentRepository extends EloquentBaseRepository implements SubDepartmentRepository
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
     * @param $subDepartmentName
     * @param $departmentId
     * @return \ArrayAccess|null
     */
    public function createOrGetSubDepartmentByName($subDepartmentName, $departmentId): ?\ArrayAccess
    {
        $subDepartment = $this->findOneBy(['name' => $subDepartmentName]);

        if (!$subDepartment) {
            $subDepartment = $this->save(['name' => $subDepartmentName, 'department_id' => $departmentId]);
        }

        return $subDepartment;
    }
}
