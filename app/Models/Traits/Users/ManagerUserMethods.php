<?php


namespace App\Models\Traits\Users;

use App\Models\Company;

trait ManagerUserMethods
{
    /**
     * is a super manager user
     *
     * @return bool
     */
    public function isSuperManager(): bool
    {
        foreach ($this->userRoles as $userRole) {
            if ($userRole->isSuperManagerUserRole()) {
                return true;
            }
        }

        return false;
    }

    /**
     * is a standard manager user
     *
     * @return bool
     */
    public function isStandardManager(): bool
    {
        foreach ($this->userRoles as $userRole) {
            if ($userRole->isStandardManagerUserRole()) {
                return true;
            }
        }
        return false;
    }

    /**
     * is a any kind of manager user
     *
     * @return bool
     */
    public function isManager(): bool
    {
        foreach ($this->userRoles as $userRole) {
            if ($userRole->hasManagerUserRole()) {
                return true;
            }
        }
        return false;
    }

    /**
     * is a manager user of the branch
     *
     * @param int $branchId
     * @return bool
     */
    public function isAManagerUserOfTheBranch(int $branchId): bool
    {
        foreach ($this->userRoles as $userRole) {
            if ($userRole->doesManagerHaveAccessToTheBranch($branchId)) {
                return true;
            }
        }

        return false;
    }
}
