<?php


namespace App\Repositories;


use App\Models\Role;
use App\Repositories\Contracts\EmployeeRepository;
use App\Repositories\Contracts\ManagerRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\UserRoleRepository;
use App\Services\Helpers\RoleHelper;
use Illuminate\Support\Facades\DB;

class EloquentEmployeeRepository extends EloquentBaseRepository implements EmployeeRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        if (isset($searchCriteria['query'])) {

            $employeeIds = $this->model->newQuery()
                ->when(isset($searchCriteria['active']), function ($q) use ($searchCriteria) {
                    $q->whereHas('user', function ($sq) use ($searchCriteria){
                        $active = $searchCriteria['active'] == "1" ? 1 : 0;
                        $sq->where('isActive', $active);
                    });
                })
                ->pluck('id')
                ->toArray();

            $searchCriteria['id'] = $this->model->newQuery()
                ->whereIn('id', $employeeIds)
                ->where(function ($q) use ($searchCriteria){
                    $q->where('level', 'like', '%' . $searchCriteria['query'] . '%')
                        ->orWhere('title', 'like', '%' . $searchCriteria['query'] . '%')
                        ->orWhereHas('user', function($query) use ($searchCriteria){
                            $query->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                                ->orWhere('email', 'like', '%' . $searchCriteria['query'] . '%')
                                ->orWhere('phone', 'like', '%' . $searchCriteria['query'] . '%');
                        });
                })
                ->pluck('id')
                ->toArray();
        }
        unset($searchCriteria['query']);
        unset($searchCriteria['active']);

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
            $roleId = Role::ROLE_EMPLOYEE_BASIC['id'];
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
            $userRoleRepository = app(UserRoleRepository::class);
            $userRoleRepository->update($admin->userRole, ['roleId' => $roleId]);
        }

        DB::commit();

        return $admin;
    }

    /**
     * @param array $data
     * @return \ArrayAccess|null
     */
    public function employeeAssignToManager(array $data): ?\ArrayAccess
    {
        $employee = $this->findOne($data['employeeId']);

        $managerData = [
            'userId' => $employee->userId,
            'branchId' => $data['branchId'],
            'level' => $data['level']
        ];

        app(ManagerRepository::class)->save($managerData);

        return $employee;
    }
}
