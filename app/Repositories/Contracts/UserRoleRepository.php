<?php


namespace App\Repositories\Contracts;


interface UserRoleRepository extends BaseRepository
{

    /**
     * get all user ids by roleId in a property
     *
     * @param int $roleId
     * @return mixed
     */
    public function getUserIdsByRoleId(int $roleId);

}
