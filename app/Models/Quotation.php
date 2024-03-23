<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quotation extends Model
{
    use CommonModelFeatures;
    const STATUS_PENDING = 'pending';
    const STATUS_SOLD = 'sold';

    const CURRENT_INVOICE_PREFIX = 'QT';

    /**
     * @var string[]
     */
    protected $fillable = [
        'createdByUserId',
        'branchId',
        'customerId',
        'invoice',
        'discount',
        'shippingCost',
        'products',
        'amount',
        'status',
        'note',
        'updatedByUserId',
    ];

    protected $casts = ['products'=>'array'];

    //generating auto invoice number
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {

            $prefix = '';
            //TODO: costly, may need to pass prefix from frontend if required
            $appSettings = AppSetting::where('branchId', request('branchId'))->where('type', AppSetting::TYPE_INVOICE)->first();

            if($appSettings instanceof AppSetting) {
                $prefix = $appSettings->settings && $appSettings->settings->invoicePrefix ? $appSettings->settings->invoicePrefix : self::CURRENT_INVOICE_PREFIX;
            }

            $model->invoice = $prefix .date("dHis") . mt_rand(100,999);
        });
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
     * get the customer
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customerId', 'id');
    }
}
