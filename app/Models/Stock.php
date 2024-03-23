<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Stock extends Model
{
    use CommonModelFeatures;

    const STATUS_AVAILABLE = 'available';
    const STATUS_OUT_OF_STOCK = 'out-of-stock';
    const STATUS_LOW = 'low';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'wcStockId',
        'productId',
        'productVariationId',
        'createdFromResourceId',
        'branchId',
        'sku',
        'quantity',
        'alertQuantity',
        'unitCost',
        'unitPrice',
        'unitProfit',
        'stockProfit',
        'discountAmount',
        'grossProfit',
        'expiredDate',
        'status',
        'updatedByUserId',
        'discountedUnitCost',
        'discountType',
        'existingUnitCost',
        'existingDiscount',
        'ecomPublishedAt',
        'permalink',
        'tax',
        'purchaseQuantity'
    ];


    //generating stock unit profit margin
    public static function boot()
    {
        parent::boot();

        self::saving(function ($model) {
            $model->unitProfit = (float)$model->unitPrice - (float) $model->unitCost;
        });
    }

    /**
     * get the product
     *
     * @return HasOne
     */
    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'productId')
            ->when(request('withDeletedStock'), function ($q) {
                $q->withTrashed();
            });
    }

    /**
     * get the product variation
     *
     * @return HasOne
     */
    public function productVariation(): HasOne
    {
        return $this->hasOne(ProductVariation::class, 'id', 'productVariationId');
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
     * get the stockLogs
     *
     * @return HasMany
     */
    public function stockLogs(): HasMany
    {
        return $this->hasMany(StockLog::class, 'stockId', 'id');
    }

    /**
     * get the orderProducts
     *
     * @return HasMany
     */
    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'stockId', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function archivedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archivedByUserId');
    }

    /**
     * @return HasManyThrough
     */
    public function productReturned(): HasManyThrough
    {
        return $this->hasManyThrough(OrderProductReturn::class, OrderProduct::class, 'stockId', 'orderProductId');
    }

    /**
     * @return HasMany
     */
    public function orderProductReturn(): HasMany
    {
        return $this->hasMany(OrderProductReturn::class, 'productId', 'productId');
    }

    /**
     * @return HasMany
     */
    public function orderProductReturnByStockId(): HasMany
    {
        return $this->hasMany(OrderProductReturn::class, 'stockId', 'id');
    }

}
