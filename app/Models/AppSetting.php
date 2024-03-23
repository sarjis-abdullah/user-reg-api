<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AppSetting extends Model
{
    use CommonModelFeatures;

    const TYPE_GENERAL = 'general';
    const TYPE_POS = 'pos';
    const TYPE_INVOICE = 'invoice';
    const TYPE_LOYALTY_REWARD = 'loyalty-reward';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'settings',
        'branchId',
    ];

    /**
     * @return mixed
     */
    public function getSettingsAttribute()
    {
        return json_decode($this->attributes['settings']);
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
}
