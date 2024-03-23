<?php


namespace App\Models\Traits\Users;


trait CommonUserMethods
{
    /**
     * has any role upto standard admin user
     *
     * @return bool
     */
    public function upToStandardAdmin(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->isStandardAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * has any role upto limited admin user
     *
     * @return bool
     */
    public function uptoLimitedAdmin(): bool
    {
        if ($this->upToStandardAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * @param string $moduleActionName
     * @return bool
     */
    public function hasModulePermission(string $moduleActionName): bool
    {
        foreach ($this->userRoles as $userRole) {
            if ($userRole->hasPermissionForModule($moduleActionName)) {
                return true;
            }
        }

        return false;
    }
}
