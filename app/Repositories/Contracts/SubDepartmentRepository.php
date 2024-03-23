<?php


namespace App\Repositories\Contracts;


interface SubDepartmentRepository extends BaseRepository
{

    /**
     * @param $subDepartmentName
     * @param $departmentId
     * @return mixed
     */
    public function createOrGetSubDepartmentByName($subDepartmentName, $departmentId);
}
