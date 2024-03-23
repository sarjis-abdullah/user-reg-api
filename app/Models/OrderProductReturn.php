<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class OrderProductReturn extends Model
{
    use CommonModelFeatures;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'branchId',
        'orderId',
        'orderProductId',
        'productId',
        'stockId',
        'quantity',
        'returnAmount',
        'profitAmount',
        'discountAmount',
        'date',
        'comment',
    ];

    /**
     * get the stock log
     *
     * @return HasOne
     */
    public function stockLog(): HasOne
    {
        return $this->hasOne(StockLog::class, 'resourceId', 'id')->where('type', StockLog::TYPE_ORDER_PRODUCT_RETURN);
    }

    /**
     * get the branch
     *
     * @return HasOne
     */
    public function branch(): HasOne
    {
        return $this->hasOne(Branch::class, 'id', 'branchId');
    }

    /**
     * get the order product
     *
     * @return HasOne
     */
    public function orderProduct(): HasOne
    {
        return $this->hasOne(OrderProduct::class, 'id', 'orderProductId');
    }

    /**
     * get the  product
     *
     */
    public function product()
    {
        return $this->orderProduct->product;
    }

    /**
     * @return BelongsTo
     */
    public function productById(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'productId', 'id');
    }

    /**
     * get the order
     *
     */
    public function order()
    {
        return $this->orderProduct->order;
    }

    /**
     * @return BelongsTo
     */
    public function orderById(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'orderId', 'id');
    }

    /**
     * get the order
     *
     */
    public function customer()
    {
        return $this->orderProduct->order->customer;
    }

    /**
     * Get the comments.
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable','paymentableType', 'paymentableId', 'id');
    }
}
