<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use CommonModelFeatures;

    const TYPE_PERCENTAGE = "percentage";
    const TYPE_FLAT = "flat";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "createdByUserId",
        "title",
        "type",
        "amount",
        "startDate",
        "endDate",
        "note",
        "updatedByUserId",
    ];
}
