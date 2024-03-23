<?php

namespace App\Models;

use App\Events\Woocommerce\TaxSavingEvent;
use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use CommonModelFeatures;

    const TYPE_PERCENTAGE = "percentage";

    const ACTION_EXCLUSIVE = "exclusive";
    const ACTION_INCLUSIVE = "inclusive";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "createdByUserId",
        "wcTaxId",
        "title",
        "amount",
        "type",
        "action",
        "notes",
        "updatedByUserId",
    ];
}
