<?php


namespace App\Repositories;

use App\Repositories\Contracts\UserRoleRepository;

class EloquentUserRoleRepository extends EloquentBaseRepository implements UserRoleRepository
{
    /**
     * @inheritDoc
     */
    public function getUserIdsByRoleId(int $roleId)
    {
        return $this->model->select(['userId'])
            ->where(['roleId' => $roleId])
            ->pluck('userId')->toArray();
    }
}
