<?php

namespace App\Repositories;

use App\Models\UserRole;
use App\Repositories\Contracts\UserProfileRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\UserRoleRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EloquentUserRepository extends EloquentBaseRepository implements UserRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {

        $searchCriteria = $this->applyFilterInUserSearch($searchCriteria);

        $searchCriteria['eagerLoad'] = ['user.roles' => 'userRoles'];

        return parent::findBy($searchCriteria, $withTrashed);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id, $withTrashed = false): ?\ArrayAccess
    {
        if ($id === 'me') {
            return $this->getLoggedInUser();
        }

        return parent::findOne($id);
    }

    /**
     * @inheritDoc
     */
    public function save(array $data): \ArrayAccess
    {
        DB::beginTransaction();

        $user = parent::save($data);

        if (isset($data['role'])) {
            $data['role']['userId'] = $user->id;
            $userRoleRepository = app(UserRoleRepository::class);
            $userRoleRepository->save($data['role']);
        }

        //TODO: will have to move userProfile creation to an event to neat this method
        $userProfileRepository = app(UserProfileRepository::class);
        $userProfileRepository->save(['userId' => $user->id]);

        DB::commit();

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function updateUser(\ArrayAccess $model, array $data): \ArrayAccess
    {
        DB::beginTransaction();

        $user = parent::update($model, $data);


        $userRoleRepository = app(UserRoleRepository::class);

        if (array_key_exists('role', $data)) {

            if (isset($data['role']['oldRoleId'])) {
                $userRole = $userRoleRepository->findOneBy(['userId' => $user->id, 'roleId' => $data['role']['oldRoleId']]);
                if ($userRole instanceof UserRole) {
                    $userRoleRepository->update($userRole, $data['role']);
                } else {
                    throw new NotFoundHttpException();
                }
            } else {
                $data['role']['userId'] = $user->id;
                //TODO, think about patch
                $userRoleRepository->save($data['role']);
            }
        }

        DB::commit();

        return $user;
    }

    /**
     * shorten the search based on search criteria
     *
     * @param $searchCriteria
     * @return mixed
     */
    private function applyFilterInUserSearch($searchCriteria)
    {
        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('email', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('name', 'like', '%' . $searchCriteria['query'] . '%')
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }

        return $searchCriteria;
    }

    /**
     * @inheritDoc
     */
    public function findUserByEmailPhone($emailOrPhone)
    {
        return $this->model->where(['email' => $emailOrPhone])
            ->orWhere(['phone' => $emailOrPhone])
            ->first();
    }
    /**
     * @inheritDoc
     */
    public function findActiveUserByEmailPhone($emailOrPhone)
    {
        return $this->model->where(['isActive' => 1])
            ->where(['email' => $emailOrPhone])
            ->orWhere(['phone' => $emailOrPhone])
            ->first();
    }

    /**
     * Logout every user
     *
     * @param $setting
     * @return bool
     */
    public function logoutEveryUser($setting): bool
    {
        $appSetting = json_decode($setting);

        if (isset($appSetting->posDisable) && $appSetting->posDisable == 'true'){
            $userIds = $this->model->where('isActive', true)->pluck('id')->toArray();
            DB::table('oauth_access_tokens')
                ->whereIn('user_id', $userIds)
                ->update(['revoked' => true]);
        }

        return true;
    }

}
