<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PurchaseProduct extends Model
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
        'productId',
        'productVariationId',
        'date',
        'quantity',
        'sku',
        'unitCost',
        'discountedUnitCost',
        'sellingPrice',
        'discountAmount',
        'discountType',
        'taxAmount',
        'totalAmount',
        'expiredDate',
        'managedByUserId',
        'updatedByUserId',
        'finalDiscountAmount',
        'returnQuantity',
        'returnTotalAmount',
        'serialIds',
        'existingUnitCost',
        'existingDiscount',
        'tax',
        'totalTaxAmount',
        'totalDiscountAmount',
        'purchaseQuantity'
    ];

    protected $casts = [
        'serialIds' => 'array'
    ];

    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = ltrim($value, '0'); // Capitalize the first letter of first_name before saving
    }

    public function setUnitCostAttribute($value)
    {
        $this->attributes['unitCost'] = ltrim($value, '0'); // Capitalize the first letter of first_name before saving
    }

    public function setSellingPriceAttribute($value)
    {
        $this->attributes['sellingPrice'] = ltrim($value, '0'); // Capitalize the first letter of first_name before saving
    }

    /**
     * get the purchase
     * @return HasOne
     */
    public function purchase(): HasOne
    {
        return $this->hasOne(Purchase::class, 'id', 'purchaseId');
    }

    /**
     * get the product variation
     *
     * @return HasOne
     */
    public function productVariation(): HasOne
    {
        return $this->hasOne(ProductVariation::class, 'id', 'productVariationId');
    }

    /**
     * get the product
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'productId', 'id');
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
     * get the product returns
     *
     * @return HasMany
     */
    public function productReturns(): HasMany
    {
        return $this->hasMany(PurchaseProductReturn::class, 'purchaseProductId', 'id');
    }

    /**
     * @return HasOne
     */
    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class, 'sku', 'sku')->where('branchId', $this->branchId)->where('productId', $this->productId);
    }

    /**
     * @return mixed
     */
    public function getReturnableQuantity()
    {

        $attachedStock = $this->stock;
        if (!$attachedStock instanceof Stock) {
            // todo - hotfix the issue
            return 0;
        }

        $stockQuantity = $attachedStock->quantity;
        $returnedQty =  $this->productReturns->sum('quantity');
        $availableReturnQty =  $this->quantity - $returnedQty;

        if(($stockQuantity >= $this->quantity) || ($stockQuantity > $availableReturnQty)) {
             return $availableReturnQty;
        } else {
            return $stockQuantity;
        }
    }

    /**
     * @return mixed
     */
    public function getReturnedQuantity()
    {
        return $this->productReturns->sum('quantity');
    }

}
