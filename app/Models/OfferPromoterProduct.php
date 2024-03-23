<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferPromoterProduct extends Model
{
    use HasFactory;

    protected $table = 'offer_promoter_products';

    protected $fillable = [
        'quantity',
        'productId',
        'bundleId',
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
