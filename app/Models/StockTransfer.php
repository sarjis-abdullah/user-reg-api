<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class StockTransfer extends Model
{
    use CommonModelFeatures;

    const STATUS_PENDING = 'PENDING';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_DECLINED = 'DECLINED';
    const STATUS_SHIPPED = 'SHIPPED';
    const STATUS_RECEIVED = 'RECEIVED';

    const STATUS_LIST = [
        self::STATUS_PENDING,
        self::STATUS_CANCELLED,
        self::STATUS_DECLINED,
        self::STATUS_SHIPPED,
        self::STATUS_RECEIVED
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'deliveryMethod',
        'deliveryId',
        'referenceNumber',
        'fromBranchId',
        'toBranchId',
        'sendingNote',
        'receivedNote',
        'comment',
        'status',
        'shippingCost',
        'shippedByUserId',
        'createdByUserId',
        'updatedByUserId'
    ];

    /**
     * get the from branch
     *
     * @return BelongsTo
     */
    public function fromBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'fromBranchId', 'id');
    }

    /**
     * get the to branch
     *
     * @return BelongsTo
     */
    public function toBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'toBranchId', 'id');
    }

    /**
     * get the stock transfer products
     *
     * @return HasMany
     */
    public function stockTransferProducts(): HasMany
    {
        return $this->hasMany(StockTransferProduct::class, 'stockTransferId', 'id');
    }

    /**
     * get the delivery
     *
     * @return BelongsTo
     */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class, 'deliveryId', 'id');
    }

    /**
     * get the user who does shipment
     *
     * @return BelongsTo
     */
    public function shippedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shippedByUserId');
    }

    public function getTotalAmountOfStockTransferProduct()
    {
        return round($this->stockTransferProducts->sum('totalAmount'),2);
    }

    public function getUnitCostAmount()
    {
        return $this->stockTransferProducts->sum(function (StockTransferProduct $stp) {
            return round($stp->unitCostToBranch * $stp->quantity, 2);
        });
    }

    public function getTotalSellingAmountOfStockTransferProduct()
    {
        return $this->stockTransferProducts->sum(function (StockTransferProduct $stp) {
            return $stp->stock->unitPrice * $stp->quantity;
        });
    }

}
