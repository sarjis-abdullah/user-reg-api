<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BranchPolicy
{
    use HandlesAuthorization;

    /**
     * Intercept checks
     *
     * @param User $currentUser
     * @return bool
     */
    public function before(User $currentUser)
    {
        if ($currentUser->isSuperAdmin()) {
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
        if ($currentUser->isManager() || $currentUser->isStandardAdmin()) {
            return true;
        }


        return $currentUser->hasModulePermission($moduleRouteName) ||
        $currentUser->hasModulePermission('franchise_list') ||
        $currentUser->hasModulePermission('warehouse_list');
    }

    /**
     * Determine if a given user has permission to store
     *
     * @param User $currentUser
     * @return bool
     */
    public function store(User $currentUser): bool
    {
        if ($currentUser->isStandardAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a given user has permission to show
     *
     * @param User $currentUser
     * @param $moduleRouteName
     * @return bool
     */
    public function show(User $currentUser, $moduleRouteName): bool
    {
        if ($currentUser->isManager() || $currentUser->isStandardAdmin()) {
            return true;
        }

        return $currentUser->hasModulePermission($moduleRouteName) ||
            $currentUser->hasModulePermission('franchise_view') ||
            $currentUser->hasModulePermission('warehouse_view');
    }

    /**
     * Determine if a given user can update
     *
     * @param User $currentUser
     * @return bool
     */
    public function update(User $currentUser): bool
    {
        if ($currentUser->isStandardAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a given user can delete
     *
     * @param User $currentUser
     * @return bool
     */
    public function destroy(User $currentUser): bool
    {
        return false;
    }
}
