<?php

namespace App\Models;

use App\Events\Woocommerce\ProductSavingEvent;
use App\Events\Woocommerce\TaxSavingEvent;
use App\Models\Traits\CommonModelFeatures;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Psy\Util\Str;

class Product extends Model
{
    use CommonModelFeatures;

    const BARCODE_TYPE_CODE_128 = 'C128'; //4445645656 Uses for barcode invoice
    const BARCODE_TYPE_CODE_C39 = 'C39'; //4445645656
    const BARCODE_TYPE_CODE_C39P = 'C39+'; //4445645656
    const BARCODE_TYPE_CODE_EAN2 = 'EAN2'; //44455656
    const BARCODE_TYPE_CODE_EAN5 = 'EAN5'; //4445656
    const BARCODE_TYPE_CODE_EAN8 = 'EAN8'; //4445
    const BARCODE_TYPE_CODE_EAN13 = 'EAN13'; //4445
    const BARCODE_TYPE_CODE_PDF417 = 'PDF417'; //4445645656

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'wcProductId',
        'companyId',
        'categoryId',
        'subCategoryId',
        'brandId',
        'discountId',
        'isDiscountApplicable',
        'name',
        'genericName',
        'selfNumber',
        'barcode',
        'taxId',
        'description',
        'expiredDate',
        'status',
        'updatedByUserId',
        'unitId',
        'alertQuantity',
        'variationOrder',
        'isSerialNumberApplicable', // if has product serial number than this key value will be true
        'departmentId',
        'subDepartmentId',
        'bundleId',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'isDiscountApplicable' => 'boolean',
        'isSerialNumberApplicable' => 'boolean'
    ];

    // Define the mutator to trim the barcode before saving
    public function setBarcodeAttribute($value)
    {
        $this->attributes['barcode'] = preg_replace('/\s+/', ' ', trim($value));
    }

    /**
     * get all the product variations
     *
     * @return HasMany
     */
    public function productVariations(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'productId', 'id');
    }

    /**
     * get the company
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'companyId');
    }

    /**
     * get the category
     *
     * @return HasOne
     */
    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'categoryId');
    }

    /**
     * get the sub category
     *
     * @return HasOne
     */
    public function subCategory(): HasOne
    {
        return $this->hasOne(SubCategory::class, 'id', 'subCategoryId');
    }

    /**
     * get the sub category
     *
     * @return HasOne
     */
    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id', 'unitId');
    }

    /**
     * get the tax
     *
     * @return HasOne
     */
    public function tax(): HasOne
    {
        return $this->hasOne(Tax::class, 'id', 'taxId');
    }

    /**
     * get the discount
     *
     * @return HasOne
     */
    public function discount(): HasOne
    {
        return $this->hasOne(Discount::class, 'id', 'discountId');
    }

    /**
     * @return bool
     */
    public function isDiscountExpired(): bool
    {
        if($this->discount) {
            if(Carbon::now()->between($this->discount->startDate,$this->discount->endDate)) {
                return false;
            }

            return true;
        }

        return true;
    }

    /**
     * get the brand
     *
     * @return HasOne
     */
    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class, 'id', 'brandId');
    }

    /**
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departmentId', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function subDepartment(): BelongsTo
    {
        return $this->belongsTo(SubDepartment::class, 'subDepartmentId', 'id');
    }

    /**
     * get the stocks
     *
     * @return HasMany
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'productId', 'id');
    }

    /**
     * @return HasMany
     */
    public function stockHas(): HasMany
    {
        return $this->hasMany(Stock::class, 'productId', 'id')
            ->where('quantity', '>', 0);
    }
    /**
     * @return BelongsTo
     */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class, 'bundleId', 'id');
    }

    /**
     * Scope a query to only include product that has stocks.
     *
     * @param Builder $query
     * @param $quantity
     * @return Builder
     */
    public function scopeHasQuantity($query, $quantity): Builder
    {
        $checked = request('acceptWithoutStock') == 1;

        if(!$checked || count($this->stocks)) {
            $comparisonRules = $quantity == 0 ? '<=' : '>=';
            return $query->whereHas('stocks', function($q) use($quantity, $comparisonRules) {
                $q->where('quantity', $comparisonRules, $quantity);
            });
        } else {
            return $query;
        }
    }

    /**
     * Scope a query to only include product that has stocks.
     *
     * @param Builder $query
     * @param $branchId
     * @return Builder
     */
    public function scopeHasBranch($query, $branchId, $onlyTrashed = false): Builder
    {
        $checked = request('acceptWithoutStock') == 1;
        $stocks = $onlyTrashed ? $this->archiveStocks : $this->stocks;
        if(!$checked || count($stocks)) {
            $relation = $onlyTrashed ? 'archiveStocks' : 'stocks';
            return $query->whereHas($relation, function($q) use ($branchId){
                $q->where('branchId', $branchId);
            });
        } else {
            return $query;
        }
    }

    /**
     * Scope a query to only include product that has stocks.
     *
     * @param Builder $query
     * @param $branchId
     * @param $quantity
     * @return Builder
     */
    public function scopeHasBranchAndQuantity($query, $branchId, $quantity): Builder
    {
        $checked = request('acceptWithoutStock') == 1;

        if(!$checked || count($this->stocks)) {
            $comparisonRules = $quantity > 0 ? '>' : '<=';
            return $query->whereHas('stocks', function($q) use($quantity, $branchId, $comparisonRules) {
                $q->where('quantity', $comparisonRules, 0);
                $q->where('branchId', $branchId);
            });
        } else {
            return $query;
        }
    }

    /**
     * Scope a query to only include product that has stocks.
     *
     * @param Builder $query
     * @param $branchId
     * @return Builder
     */
    public function scopeHasStockAlert($query): Builder
    {
        $alertQty = $this->alertQuantity;

        return $query->whereHas('stocks', function($q) use ($alertQty) {
            $q->where('quantity', '<=', $alertQty);
        });
    }

    /**
     * Scope a query to only include product that has stocks.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeHasStockExpiration($query, $endDate): Builder
    {
        return $query->whereHas('stocks', function($q) use ($endDate) {
            $q->where('expiredDate', '<=', $endDate);
        });
    }

    /**
     * get the barcode image
     *
     * @return HasOne
     */
    public function barcodeImage(): HasOne
    {
        return $this->hasOne(Attachment::class, 'resourceId', 'id')
            ->where('type', Attachment::ATTACHMENT_TYPE_PRODUCT_BARCODE)
            ->latest();
    }

    /**
     * get the product image
     *
     * @return HasOne
     */
    public function image(): HasOne
    {
        return $this->hasOne(Attachment::class, 'resourceId', 'id')
            ->where('type', Attachment::ATTACHMENT_TYPE_PRODUCT)
            ->latest();
    }

    public function archiveStocks(): HasMany
    {
        return $this->stocks()->onlyTrashed();
    }

    public function archivedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archivedByUserId');
    }

    /**
     * @return HasMany
     */
    public function stocksForWc(): HasMany
    {
        $stocks = $this->stocks()
            ->when(request()->filled('branchId'),
                fn($query) => $query->where('branchId', request()->get('branchId')))
            ->when(request()->filled('sku'),
                fn($query) => $query->where('sku', request()->get('sku')));

        if (request()->filled('acceptWithoutStock')){
            return $stocks;
        }else{
            return $stocks->where('quantity', '>', 0);
        }
    }

    /**
     * @return HasMany
     */
    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'productId', 'id');
    }
    /**
     * @return HasMany
     */
    public function orderProductReturns(): HasMany
    {
        return $this->hasMany(OrderProductReturn::class, 'productId', 'id');
    }
}
