<?php

namespace App\Models\Traits;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

trait CommonModelFeatures
{
    use SoftDeletes, HasFactory, CommonModelHelperFeatures, Cachable;

    //Used in Cachable
    protected $cachePrefix = "pos-model-cache";
}
