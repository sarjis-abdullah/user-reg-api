<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttachmentPolicy
{
    use HandlesAuthorization;

    /**
     * Intercept checks
     *
     * @param User $currentUser
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
     * @return bool
     */
    public function list(User $currentUser): bool
    {
        if ($currentUser->isSuperManager()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a given user has permission to show
     *
     * @param User $currentUser
     * @param Attachment $attachment
     * @return bool
     */
    public function show(User $currentUser,  Attachment $attachment): bool
    {
        if ($currentUser->isStandardManager()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a given user can delete
     *
     * @param User $currentUser
     * @param $moduleRouteName
     * @return bool
     */
    public function destroy(User $currentUser, $moduleRouteName): bool
    {
        return $currentUser->hasModulePermission($moduleRouteName);
    }
}
