<?php

namespace App\Models;

use App\Events\Woocommerce\CategorySavingEvent;
use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
    use CommonModelFeatures;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'wcCategoryId',
        'name',
        'code',
        'details',
        'updatedByUserId',
    ];

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'categoryId', 'id');
    }

    /**
     * @return HasManyThrough
     */
    public function orderProducts(): HasManyThrough
    {
        return $this->hasManyThrough(OrderProduct::class, Product::class, 'categoryId', 'productId');
    }

    /**
     * @return HasManyThrough
     */
    public function orderProductReturn(): HasManyThrough
    {
        return $this->hasManyThrough(OrderProductReturn::class, Product::class, 'categoryId', 'productId');
    }
}
