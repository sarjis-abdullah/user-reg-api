<?php


namespace App\Models\Traits\UserRoles;


trait AdminRoleMethods
{
    /**
     * is a super admin user
     *
     * @return boolean
     */
    public function isSuperAdminUserRole(): bool
    {
        return $this->role->isSuperAdminRole();
    }

    /**
     * is a standard admin user
     *
     * @return boolean
     */
    public function isStandardAdminUserRole(): bool
    {
        return $this->role->isStandardAdminRole();
    }

    /**
     * is a limited admin user
     *
     * @return boolean
     */
    public function isLimitedAdminUserRole(): bool
    {
        return $this->role->isLimitedAdminRole();
    }

    /**
     * has any admin user role
     *
     * @return boolean
     */
    public function hasAdminUserRole(): bool
    {
        return $this->role->hasAdminRole();
    }
}
