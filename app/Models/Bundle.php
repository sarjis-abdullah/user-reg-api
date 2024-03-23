<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bundle extends Model
{
    use HasFactory;

    const CUSTOMER_BUYS_PRODUCTS = "product";
    const CUSTOMER_GETS_PRODUCTS = "product";
    const CUSTOMER_GETS_AMOUNT_OFF = "amount-off";
    const CUSTOMER_ELIGIBLE_TYPE = "all-customer";

    const OFFER_COMBINES_WITH = ["product", "order", "shipping", "product-order", "product-shipping", "order-shipping", "all"];

    protected $fillable = [
        'customerBuys',
        'customerGets',
        'offerCombinesWith',
        'eligibleCustomerType',
        'usesPerOrderLimit',
        'usesPerUserLimit',
        'usageLimit',
        'offerStartsAt',
        'offerEndsAt',
    ];

    /**
     * @return HasMany
     */
    public function offerPromoterProducts(): HasMany
    {
        return $this->hasMany(OfferPromoterProduct::class, 'bundleId', 'id');
    }
    /**
     * @return HasMany
     */
    public function offerProducts(): HasMany
    {
        return $this->hasMany(OfferProduct::class, 'bundleId', 'id');
    }
}
