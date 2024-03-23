<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory;

    const DELIVERY_TYPE_STOCK_TRANSFER = 'STOCK_TRANSFER';
    const DELIVERY_TYPE_SALE = 'SALE_ORDER';
    const DELIVERY_TYPE_PURCHASE = 'PURCHASE_ORDER';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'deliveryAgencyId',
        'deliveryPersonName',
        'deliveryPersonId',
        'trackingNumber',
        'fromDeliveryPhone',
        'toDeliveryPhone',
        'fromDeliveryAddress',
        'toDeliveryAddress',
        'status',
        'note',
        'createdByUserId',
        'updatedByUserId'
    ];

    /**
     * get the delivery method
     *
     * @return BelongsTo
     */
    public function deliveryAgency(): BelongsTo
    {
        return $this->belongsTo(DeliveryAgency::class, 'deliveryAgencyId', 'id');
    }
}
