<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use CommonModelFeatures;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'name',
        'isActive',
        'updatedByUserId',
    ];


    /**
     * get the moduleActions
     *
     * @return HasMany
     */
    public function moduleActions(): HasMany
    {
        return $this->hasMany(ModuleAction::class, 'moduleId', 'id');
    }
}
