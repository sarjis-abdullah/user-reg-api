<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use CommonModelFeatures, HasUuid;

    const REFERENCE_TYPE_ID_STOCK = 1;
    const REFERENCE_TYPE_ID_PRODUCTS_EXPIRED = 2;
    const REFERENCE_TYPE_ID_STOCK_TRANSFER = 3;
    const REFERENCE_TYPE_ID_SEND_REPORT = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
        'notified_at',
        'created_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * @return MorphTo
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
