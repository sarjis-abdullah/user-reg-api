<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariation extends Model
{
    use CommonModelFeatures;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'productId',
        'size',
        'color',
        'material',
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'productId', 'id');
    }

    /**
     * get all the stocks with this product variation
     *
     * @return HasMany
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'productVariationId', 'id')
            ->where('productId', $this->productId);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        $variationOrder = $this->product->variationOrder;

        $variationOrderArray = explode('/', $variationOrder);

        $sortedVariations = [];
        foreach ($variationOrderArray as $attribute) {
            if ($this->getAttribute($attribute)) {
                $sortedVariations[] = $this->getAttribute($attribute);
            }
        }

        return implode('/', $sortedVariations);
    }
}
