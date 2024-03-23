<?php


namespace App\Repositories;


use App\Models\Role;
use App\Repositories\Contracts\ManagerRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\UserRoleRepository;
use App\Services\Helpers\RoleHelper;
use Illuminate\Support\Facades\DB;

class EloquentManagerRepository extends EloquentBaseRepository implements ManagerRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('title', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhereHas('user', function($query) use ($searchCriteria){
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                        ->orWhere('email', 'like', '%' . $searchCriteria['query'] . '%')
                        ->orWhere('phone', 'like', '%' . $searchCriteria['query'] . '%');
                })
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }
        return $this->findByWithDateRanges($searchCriteria, $withTrashed, true);
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
            $roleId = Role::ROLE_MANAGER_STANDARD['id'];
        }

        //create an user role
        $userRoleRepository = app(UserRoleRepository::class);
        $userRole = $userRoleRepository->save(['roleId' => $roleId, 'branchId' => $data['branchId'], 'userId' => $data['userId']]);

        $data['userRoleId'] = $userRole->id;

        $manager = parent::save($data);

        DB::commit();

        return $manager;
    }

    /**
     * @inherit Doc
     */
    public function update(\ArrayAccess $model, array $data): \ArrayAccess
    {
        DB::beginTransaction();

        $manager = parent::update($model, $data);

        $userRepository = app(UserRepository::class);

        if(isset($data['user'])) {
            $userRepository->updateUser($manager->user, $data['user']);
        }

        if (isset($data['level'])) {
            $roleId = RoleHelper::getRoleIdByTitle($data['level']);
            $userRoleRepository = app(UserRoleRepository::class);
            $userRoleRepository->update($manager->userRole, ['roleId' => $roleId, 'branchId' => $data['branchId']]);
        }

        DB::commit();

        return $manager;
    }
}
