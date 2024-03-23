<?php

namespace App\Policies;

use App\Models\StockTransfer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockTransferPolicy
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
        if ($currentUser->isStandardAdmin() || $currentUser->isManager()) {
            return true;
        }

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
        if ($currentUser->isStandardAdmin()) {
            return true;
        }

        return $currentUser->hasModulePermission($moduleRouteName);
    }

    /**
     * Determine if a given user has permission to show
     *
     * @param User $currentUser
     * @param StockTransfer $stockTransfer
     * @param $moduleRouteName
     * @return bool
     */
    public function show(User $currentUser,  StockTransfer $stockTransfer, $moduleRouteName): bool
    {
        if ($currentUser->isStandardAdmin() || $currentUser->isManager()) {
            return true;
        }

        return $currentUser->hasModulePermission($moduleRouteName);
    }

    /**
     * Determine if a given user can update
     *
     * @param User $currentUser
     * @param StockTransfer $stockTransfer
     * @param $moduleRouteName
     * @return bool
     */
    public function update(User $currentUser, StockTransfer $stockTransfer, $moduleRouteName): bool
    {
        if ($currentUser->isStandardAdmin()) {
            return true;
        }

        return $currentUser->hasModulePermission($moduleRouteName);
    }
}
