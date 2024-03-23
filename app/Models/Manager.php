<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Manager extends Model
{
    use CommonModelFeatures;

    const LEVEL_SUPER = 'super_manager';
    const LEVEL_STANDARD = 'standard_manager';
    const LEVEL_RESTRICTED = 'restricted_manager';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'userId',
        'userRoleId',
        'companyId',
        'branchId',
        'title',
        'level',
        'updatedByUserId',
    ];

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
     * get the user's role
     *
     * @return HasOne
     */
    public function userRole(): HasOne
    {
        return $this->hasOne(UserRole::class, 'id', 'userRoleId');
    }

    /**
     * get the company
     *
     * @return HasOne
     */
    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'companyId');
    }

    /**
     * get the branch
     *
     * @return HasOne
     */
    public function branch(): HasOne
    {
        return $this->hasOne(Branch::class, 'id', 'branchId');
    }

    /**
     * get the user roles
     *
     * @return Hasmany
     */
    public function userRoles(): Hasmany
    {
        return $this->hasMany(UserRole::class, 'userId', 'userId');
    }


    /**
     * @return HasMany
     */
    public function order(): HasMany
    {
        return $this->hasMany(Order::class, 'createdByUserId', 'userId');
    }
}
