<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderProduct extends Model
{
    use CommonModelFeatures;

    const STATUS_DELIVERED = 'delivered';
    const STATUS_RETURNED = 'returned';
    const STATUS_PARTIAL_RETURNED = 'partial-returned';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'productId',
        'stockId',
        'orderId',
        'date',
        'quantity',
        'unitPrice',
        'discountedUnitPrice',
        'discount',
        'discountId',
        'tax',
        'taxId',
        'amount',
        'profitAmount',
        'grossProfit',
        'size',
        'color',
        'status',
        'updatedByUserId',
    ];

    //generating order product profit amount
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {

            if(!isset($model->profitAmount) && empty($model->profiAmount)) {
                $stock = Stock::where('id', $model->stockId)->first();
                $model->profitAmount = $model->quantity * $stock->unitProfit;
            }

            $model->grossProfit = $model->profitAmount - $model->discount;
        });
    }

    /**
     * get the stock
     *
     * @return HasOne
     */
    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class, 'id', 'stockId');
    }

    /**
     * get the stock log
     *
     * @return HasOne
     */
    public function stockLog(): HasOne
    {
        return $this->hasOne(StockLog::class, 'resourceId', 'id')->where('type', StockLog::TYPE_ORDER_PRODUCT);
    }

    /**
     * get the product
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'productId', 'id');
    }

    /**
     * get the tax
     * duplicated method
     * @return HasOne
     */
    public function getTax(): HasOne
    {
        return $this->hasOne(Tax::class, 'id', 'taxId');
    }

    /**
     * get the discount
     * duplicated
     * @return HasOne
     */
    public function getDiscount(): HasOne
    {
        return $this->hasOne(Discount::class, 'id', 'discountId');
    }

    /**
     * get the order
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'orderId', 'id');
    }

    /**
     * get the branch
     *
     * @return BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branchId', 'id');
    }

    /**
     * get the product returns
     *
     * @return HasMany
     */
    public function productReturns(): HasMany
    {
        return $this->hasMany(OrderProductReturn::class, 'orderProductId', 'id');
    }

    /**
     * @return mixed
     */
    public function getReturnedQuantity()
    {
        return $this->productReturns->sum('quantity');
    }
}
