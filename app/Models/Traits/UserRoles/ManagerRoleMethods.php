<?php


namespace App\Models\Traits\UserRoles;


trait ManagerRoleMethods
{
    /**
     * is a super manager user role
     *
     * @return boolean
     */
    public function isSuperManagerUserRole(): bool
    {
        return $this->role->isSuperManagerRole();
    }

    /**
     * is a standard manager user role
     *
     * @return boolean
     */
    public function isStandardManagerUserRole(): bool
    {
        return $this->role->isStandardManagerRole();
    }

    /**
     * has any manager user role
     *
     * @return boolean
     */
    public function hasManagerUserRole(): bool
    {
        return $this->role->hasManagerRole();
    }


    /**
     * does the manager have access to the Branch
     *
     * @param int $branchId
     * @return bool
     */
    public function doesManagerHaveAccessToTheBranch(int $branchId): bool
    {
        return $this->hasManagerUserRole() && $this->hasTheBranchAssigned($branchId);
    }
}
