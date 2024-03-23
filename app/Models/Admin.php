<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Admin extends Model
{
    use CommonModelFeatures;

    //FYI, it has to be matched with its corresponding user role
    const LEVEL_SUPER = 'super_admin';
    const LEVEL_STANDARD = 'standard_admin';
    const LEVEL_LIMITED = 'limited_admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'userId',
        'userRoleId',
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
     * get the user role
     *
     * @return HasOne
     */
    public function userRole(): HasOne
    {
        return $this->hasOne(UserRole::class, 'id', 'userRoleId');
    }
}
