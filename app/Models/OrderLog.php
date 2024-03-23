<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    use CommonModelFeatures;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'orderId',
        'comment',
        'status',
        'paymentStatus',
        'deliveryStatus',
        'updatedByUserId',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'orderId', 'id');
    }
}
