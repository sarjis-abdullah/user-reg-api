<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppSettingPolicy
{
    use HandlesAuthorization;

    /**
     * Intercept checks
     *
     * @param User $currentUser
     * @return bool|void
     */
    public function before(User $currentUser)
    {
        if ($currentUser->upToStandardAdmin()) {
            return true;
        }
    }

    /**
     * Determine if a given user has permission to list
     *
     * @param User $currentUser
     * @param $moduleRouteName
     * @return bool
     */
    public function list(User $currentUser, $moduleRouteName): bool
    {
        return $currentUser->hasModulePermission($moduleRouteName);
    }

    /**
     * Determine if a given user has permission to store
     *
     * @param User $currentUser
     * @param $moduleRouteName
     * @return bool
     */
    public function store(User $currentUser, $moduleRouteName): bool
    {
        return $currentUser->hasModulePermission($moduleRouteName);
    }

    /**
     * Determine if a given user has permission to show
     *
     * @param User $currentUser
     * @param $moduleRouteName
     * @return bool
     */
    public function show(User $currentUser,  $moduleRouteName): bool
    {
        return $currentUser->hasModulePermission($moduleRouteName);
    }
}
