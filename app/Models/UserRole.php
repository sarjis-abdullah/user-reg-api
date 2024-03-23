<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use App\Models\Traits\UserRoles\AdminRoleMethods;
use App\Models\Traits\UserRoles\ManagerRoleMethods;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserRole extends Model
{
    use CommonModelFeatures, AdminRoleMethods, ManagerRoleMethods;

    /**
     * Table name
     * @var string
     */
    protected $table = 'user_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'roleId',
        'userId',
        'branchId',
    ];

    /**
     * get the user
     *
     * @return HasOne
     */
    public function permissions(): HasOne
    {
        return $this->hasOne(UserRoleModulePermission::class, 'roleId', 'roleId');
    }

    /**
     * get the user
     *
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'userId');
    }

    /**
     * get the role of the user
     *
     * @return HasOne
     */
    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id', 'roleId');
    }


    /**
     * get the branch related to the user's role
     *
     * @return hasOne
     */
    public function branch(): HasOne
    {
        return $this->hasOne(Branch::class, 'id', 'branchId');
    }

    /**
     * has the user's role assigned to the branch
     *
     * @param int $branchId
     * @return boolean
     */
    public function hasTheBranchAssigned(int $branchId): bool
    {
        return $this->branch->id === $branchId;
    }

    /**
     * has the user's role permitted for the module
     *
     * @param string $moduleActionName
     * @return boolean
     */
    public function hasPermissionForModule(string $moduleActionName): bool
    {
        return in_array($moduleActionName, $this->permissions->moduleActionNames);
    }
}
