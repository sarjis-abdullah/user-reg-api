<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferProduct extends Model
{
    use HasFactory;

    const DISCOUNT_TYPE_FREE = "free";
    const DISCOUNT_TYPE_FLAT = "flat";
    const DISCOUNT_TYPE_PERCENTAGE = "percentage";

    protected $fillable = [
        'quantity',
        'productId',
        'bundleId',
        'discountType',
        'discountAmount',
        'stockId'
    ];

    function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'productId', 'id');
    }
    function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'offerId', 'id');
    }

}
