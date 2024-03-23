<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserProfilePolicy
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
        if ($currentUser->isSuperAdmin()) {
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
     * @param int $userId
     * @return bool
     */
    public function store(User $currentUser, int $userId): bool
    {
        if($currentUser->id === $userId) {
            return true;
        };

        return false;
    }

    /**
     * Determine if a given user has permission to show
     *
     * @param User $currentUser
     * @param UserProfile $userProfile
     * @return bool
     */
    public function show(User $currentUser,  UserProfile $userProfile): bool
    {
        if ($currentUser->id === $userProfile->userId) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a given user can update
     *
     * @param User $currentUser
     * @param UserProfile $userProfile
     * @return bool
     */
    public function update(User $currentUser, UserProfile $userProfile): bool
    {
        return $currentUser->id === $userProfile->userId;
    }

    /**
     * Determine if a given user can delete
     *
     * @param User $currentUser
     * @param User $user
     * @return bool
     */
    public function destroy(User $currentUser, User $user): bool
    {
        return false;
    }

}
