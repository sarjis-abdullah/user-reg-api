<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use CommonModelFeatures;

    const TO_ALL_CUSTOMER = 'all-customer';
    const TO_GROUP_CUSTOMER = 'group-customer';
    const TO_INDIVIDUAL_CUSTOMER = 'individual-customer';

    const TYPE_ON_ORDER = 'on-order';
    const TYPE_ON_ORDER_PRODUCTS = 'on-order-products';
    const TYPE_ON_DELIVERY = 'on-delivery';
    const TYPE_ON_VAT = 'on-vat';

    const AMOUNT_TYPE_FLAT = 'flat';
    const AMOUNT_TYPE_PERCENTAGE = 'percentage';

    const USED_IN_POS = 'pos';
    const USED_IN_ECOMMERCE = 'ecommerce';

    const STATUS_DRAFT = 'draft';
    const STATUS_EXPIRED = 'expired';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'title',
        'code',
        'description',
        'to',
        'type',
        'amount',
        'amountType',
        'minTxnAmount',
        'maxDiscountAmount',
        'usedIn',
        'maxCouponUsage',
        'startDate',
        'expirationDate',
        'status',
        'updatedByUserId',
    ];

    /**
     * get all the coupon customers
     *
     * @return HasMany
     */
    public function couponCustomers(): HasMany
    {
        return $this->hasMany(CouponCustomer::class, 'couponId', 'id');
    }
}
