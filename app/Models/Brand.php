<?php

namespace App\Models;

use App\Events\Woocommerce\BrandSavingEvent;
use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Brand extends Model
{
    use CommonModelFeatures;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'wcBrandId',
        'companyId',
        'name',
        'status',
        'origin',
        'details',
        'updatedByUserId',
    ];

    /**
     * get the company
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'companyId', 'id');
    }
}
