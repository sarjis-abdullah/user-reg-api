<?php


namespace App\Repositories\Contracts;


interface DepartmentRepository extends BaseRepository
{
    /**
     * create or get company by name
     *
     * @param string $departmentName
     * @return \ArrayAccess|null
     */
    public function createOrGetDepartmentByName($departmentName);
}
