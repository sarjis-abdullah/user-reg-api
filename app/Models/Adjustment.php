<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Adjustment extends Model
{
    use CommonModelFeatures;

    const TYPE_INCREMENT = "increment";
    const TYPE_DECREMENT = "decrement";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "branchId",
        "stockId",
        "quantity",
        "reason",
        "date",
        "type",
        "adjustmentBy",
        "createdByUserId",
        "updatedByUserId",
    ];

    /**
     * get the branch
     *
     * @return BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branchId');
    }

    /**
     * get the stock
     *
     * @return BelongsTo
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stockId')
            ->when(request('withDeletedStock'), function ($q) {
                $q->withTrashed();
            });
    }

    /**
     * @return HasOneThrough
     */
    public function product(): HasOneThrough
    {
        return $this->hasOneThrough(Product::class, Stock::class, 'id', 'id', 'stockId', 'productId');
    }
}
