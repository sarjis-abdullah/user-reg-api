<?php


namespace App\Repositories;


use App\Models\Admin;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\AdminRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\UserRoleRepository;
use App\Services\Helpers\RoleHelper;
use Illuminate\Support\Facades\DB;

class EloquentAdminRepository extends EloquentBaseRepository implements AdminRepository
{
    /**
     * @inheritDoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        $searchCriteria = $this->applyFilterInUserSearch($searchCriteria);

        return parent::findBy($searchCriteria, $withTrashed);
    }

    /**
     * @inherit Doc
     */
    public function save(array $data): \ArrayAccess
    {
        DB::beginTransaction();

        if(isset($data['user'])) {
            $userRepository = app(UserRepository::class);
            $user = $userRepository->save($data['user']);
            $data['userId'] = $user->id;
        }

        if (array_key_exists('level', $data)) {
            $roleId = RoleHelper::getRoleIdByTitle($data['level']);
        } else {
            $roleId = Role::ROLE_ADMIN_STANDARD['id'];
        }

        //create an user role
        $userRoleRepository = app(UserRoleRepository::class);
        $userRole = $userRoleRepository->save(['roleId' => $roleId, 'userId' => $data['userId']]);

        $data['userRoleId'] = $userRole->id;

        $admin = parent::save($data);
        DB::commit();

        return $admin;
    }

    /**
     * @inherit Doc
     */
    public function update(\ArrayAccess $model, array $data): \ArrayAccess
    {
        DB::beginTransaction();

        $admin = parent::update($model, $data);

        $userRepository = app(UserRepository::class);

        if(isset($data['user'])) {
            $userRepository->updateUser($admin->user, $data['user']);
        }

        if (isset($data['level'])) {
            $roleId = RoleHelper::getRoleIdByTitle($data['level']);
            // update user role
            $userRoleRepository = app(UserRoleRepository::class);
            $userRoleRepository->update($admin->userRole, ['roleId' => $roleId]);
        }

        DB::commit();

        return $admin;
    }

    /**
     * shorten the search based on search criteria
     *
     * @param $searchCriteria
     * @return mixed
     */
    private function applyFilterInUserSearch($searchCriteria)
    {
        if (!$this->getLoggedInUser()->isSuperAdmin()) {
            $searchCriteria['level'] = Admin::LEVEL_LIMITED;
        }

        if (isset($searchCriteria['withName'])) {
            $searchCriteria['id'] = $this->getAdminUserIdsByName($searchCriteria);
            unset($searchCriteria['withName']);
        }

        if (isset($searchCriteria['id'])) {
            $searchCriteria['id'] = implode(",", array_unique($searchCriteria['id']));
        }

        return $searchCriteria;
    }

    /**
     * @inheritDoc
     */
    public function getAdminUserIdsByName(array $searchCriteria = [])
    {
        $thisModelTable = $this->model->getTable();
        $userModelTable = User::getTableName();

        return $this->model
            ->select($thisModelTable . '.id')
            ->join($userModelTable, $userModelTable . '.id', '=', $thisModelTable . '.userId')
            ->where($userModelTable . '.name', 'like', '%' . $searchCriteria['withName'] . '%')
            ->orWhere($userModelTable . '.email', 'like', '%' . $searchCriteria['withName'] . '%')
            ->pluck('id')->toArray();
    }
}
