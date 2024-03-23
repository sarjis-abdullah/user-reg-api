<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use CommonModelFeatures;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPEND = 'suspend';

    const TYPE_WAREHOUSE = 'warehouse'; //act as warehouse
    const TYPE_SELF = 'self'; //act as branch
    const TYPE_FRANCHISE = 'franchise'; //act as agent/subbranch of branch
    const TYPE_ECOMMERCE = 'ecommerce'; //act as agent/subbranch of branch

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'type',
        'name',
        'address',
        'website',
        'email',
        'phone',
        'status',
        'details',
        'updatedByUserId',
    ];

    /**
     * @param $status
     * @return void
     */
    public function setStatusAttribute($status)
    {
        $this->attributes['status'] = $status ?? self::STATUS_ACTIVE;
    }

    /**
     * get all the users roles as admin access
     *
     * @return HasMany
     */
    public function adminUserRoles(): HasMany
    {
        return $this->hasMany(UserRole::class, 'branchId', 'id')
            ->whereIn('roleId', [Role::ROLE_ADMIN_LIMITED['id'], Role::ROLE_ADMIN_STANDARD['id'], Role::ROLE_MANAGER_SUPER['id'], Role::ROLE_MANAGER_STANDARD['id']]);
    }
}
