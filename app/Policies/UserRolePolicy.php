<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserRolePolicy
{
    use HandlesAuthorization;

    /**
     * Intercept checks
     *
     * @param User $currentUser
     * @return bool
     */
    public function before(User $currentUser): bool
    {
        if ($currentUser->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine if a given user has permission to list
     *
     * @param User $currentUser
     * @return bool
     */
    public function list(User $currentUser): bool
    {
        if ($currentUser->isStandardAdmin()) {
            return true;
        }

        return false;
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
     * @param UserRole $userRole
     * @return bool
     */
    public function show(User $currentUser,  UserRole $userRole): bool
    {
        return $currentUser->id === $userRole->userId;
    }

    /**
     * Determine if a given user can update
     *
     * @param User $currentUser
     * @param UserRole $userRole
     * @return bool
     */
    public function update(User $currentUser, UserRole $userRole): bool
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
     * @param UserRole $userRole
     * @return bool
     */
    public function destroy(User $currentUser, UserRole $userRole): bool
    {
        return false;
    }
}
