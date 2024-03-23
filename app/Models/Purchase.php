<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Purchase extends Model
{
    use CommonModelFeatures;

    const STATUS_RECEIVED = 'received';
    const STATUS_PENDING = 'pending';
    const STATUS_ORDERED = 'ordered';
    const STATUS_HOLD = 'hold';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'supplierId',
        'branchId',
        'reference',
        'totalAmount',
        'discountAmount',
        'shippingCost',
        'taxAmount',
        'note',
        'paid',
        'returnedAmount',
        'due',
        'gettableDueAmount',
        'date',
        'status',
        'paymentStatus',
        'updatedByUserId',
    ];

    //generating auto invoice number
    public static function booted()
    {
        parent::booted();

        self::created(function ($model) {
            //TODO: review the str_pad in near future
            $model->reference = 'PU' . str_pad($model->id, strlen($model->id) >= 6 ? 9 : 6, '0', STR_PAD_LEFT);
            $model->save();
        });
    }

    /**
     * @param $prefix
     * @param $id
     * @param $unitCost
     * @return string
     */
    public static function generateSku($prefix, $id, $unitCost): string
    {
        return sprintf('%s-%s-%s', substr($prefix, 0, 3), $id, str_replace('.', '', $unitCost));
    }

    /**
     * get the order products
     *
     * @return HasMany
     */
    public function purchaseProducts(): HasMany
    {
        return $this->hasMany(PurchaseProduct::class, 'purchaseId', 'id');
    }

    /**
     * Get the comments.
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable','paymentableType', 'paymentableId', 'id');
    }

    /**
     * get the purchase paymentMethods
     *
     */
    public function paymentMethods(): string
    {
        $methods = $this->payments()->pluck('method')->implode(',');
        return implode(',', array_unique(explode(',', $methods)));
    }

    /**
     * get the supplier
     *
     * @return HasOne
     */
    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class, 'id', 'supplierId');
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
     * get the invoice image
     *
     * @return HasOne
     */
    public function referenceImage(): HasOne
    {
        return $this->hasOne(Attachment::class, 'resourceId', 'id')
            ->where('type', Attachment::ATTACHMENT_TYPE_REFERENCE)
            ->latest();
    }

    /**
     * get the purchase product returns
     *
     * @return HasManyThrough
     */
    public function purchaseProductReturns(): HasManyThrough
    {
        return $this->hasManyThrough(PurchaseProductReturn::class, PurchaseProduct::class, 'purchaseId', 'purchaseProductId');
    }

    /**
     * @return float|int|mixed
     */
    public function getTotalReturnAmount()
    {
        return count($this->purchaseProductReturns) ? $this->purchaseProductReturns->sum('returnAmount') : 0.0;
    }

    /**
     * @return float|int|mixed
     */
    public function getDueAmount()
    {
        if($this->due > $this->getTotalReturnAmount()) {
            return $this->due - $this->getTotalReturnAmount();
        }

        return $this->due;
    }
}
