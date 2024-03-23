<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Testing\Fluent\Concerns\Has;

class StockTransferProduct extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fromBranchId',
        'toBranchId',
        'productId',
        'increaseCostPriceAmount',
        'unitCostToBranch',
        'stockTransferId',
        'quantity',
        'totalAmount',
        'comment',
        'date',
        'status',
        'sku',
        'createdByUserId',
        'updatedByUserId'
    ];

    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = ltrim($value, '0'); // Capitalize the first letter of first_name before saving
    }

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
     * get the to branch
     *
     * @return BelongsTo
     */
    public function stockTransfer(): BelongsTo
    {
        return $this->belongsTo(StockTransfer::class, 'stockTransferId', 'id');
    }

    /**
     * get the product
     *
     * @return HasOne
     */
    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'productId');
    }

    /**
     * get the product
     *
     * @return HasOne
     */
    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class, 'sku', 'sku')
            ->where('productId', $this->productId)
            ->where('branchId', $this->fromBranchId)
            ->withTrashed()
            ->latest();
    }
}
