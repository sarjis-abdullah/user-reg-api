<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;

class GenericExport extends Model
{
    use CommonModelFeatures;

    /**
     * @var string[]
     */
    protected $fillable = [
        'createdByUserId',
        'items',
        'status',
        'viewPath',
        'fileName',
        'statusMessage',
        'exportAs'
    ];
}
