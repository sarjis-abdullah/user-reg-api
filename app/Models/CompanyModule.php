<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyModule extends Model
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
        'companyId',
        'activationDate',
        'isActive',
        'updatedByUserId',
    ];

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
     * get the module
     *
     * @return HasOne
     */
    public function module(): HasOne
    {
        return $this->hasOne(Module::class, 'id', 'moduleId');
    }
}
