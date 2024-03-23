<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ModuleAction extends Model
{
    use CommonModelFeatures;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'moduleId',
        'name',
        'hasAccessUpToRoleId',
        'updatedByUserId',
    ];

    /**
     * get the module
     *
     * @return HasOne
     */
    public function module(): HasOne
    {
        return $this->hasOne(Module::class, 'id', 'moduleId');
    }

    /**
     * get the hasAccessUpToRole
     *
     * @return HasOne
     */
    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id', 'hasAccessUpToRoleId');
    }

}
