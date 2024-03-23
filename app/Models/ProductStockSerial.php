<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStockSerial extends Model
{
    use CommonModelFeatures;

    const STATUS_AVAILABLE = 'available';
    const STATUS_SOLD_OUT = 'sold_out';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'productId', 'stockId', 'productStockSerialId', 'createdByUserId', 'updatedByUserId', 'status', 'created_at', 'updated_at'
    ];

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'productId', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stockId', 'id');
    }

}
