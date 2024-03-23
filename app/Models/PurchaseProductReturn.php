<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PurchaseProductReturn extends Model
{
    use CommonModelFeatures;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'branchId',
        'purchaseId',
        'purchaseProductId',
        'quantity',
        'returnAmount',
        'date',
        'comment',
    ];

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
     * get the purchase product
     *
     * @return HasOne
     */
    public function purchaseProduct(): HasOne
    {
        return $this->hasOne(PurchaseProduct::class, 'id', 'purchaseProductId');
    }

    /**
     * @return mixed
     */
    public function purchase()
    {
        return $this->purchaseProduct->purchase;
    }

    /**
     * Get the comments.
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable','paymentableType', 'paymentableId', 'id');
    }
}
