<?php


namespace App\Models\Traits\Users;


trait AdminUserMethods
{
    /**
     * is a super admin user
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        foreach ($this->userRoles as $userRole) {
            if ($userRole->isSuperAdminUserRole()) {
                return true;
            }
        }

        return false;
    }

    /**
     * is a standard admin user
     *
     * @return bool
     */
    public function isStandardAdmin(): bool
    {
        foreach ($this->userRoles as $userRole) {
            if ($userRole->isStandardAdminUserRole()) {
                return true;
            }
        }

        return false;
    }

    /**
     * is a limited admin user
     *
     * @return bool
     */
    public function isLimitedAdmin(): bool
    {
        foreach ($this->userRoles as $userRole) {
            if ($userRole->isLimitedAdminUserRole()) {
                return true;
            }
        }

        return false;
    }

    /**
     * is a any kind of admin user
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        foreach ($this->userRoles as $userRole) {
            if ($userRole->hasAdminUserRole()) {
                return true;
            }
        }

        return false;
    }
}
