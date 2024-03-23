<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use App\Repositories\Contracts\SupplierRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Supplier extends Model
{
    use CommonModelFeatures;

    const TYPE_REGULAR = 'regular';
    const TYPE_WALK_IN = 'walk-in';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'companyId',
        'name',
        'agencyName',
        'categories',
        'type',
        'email',
        'phone',
        'address',
        'status',
        'updatedByUserId',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'categories' => 'json'
    ];


    /**
     * get the company
     *
     * @return HasOne
     */
    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'companyId');
    }

    /**
     * get the purchases
     *
     * @return HasMany
     */
    public function purchases(): HasMany
    {
        $queryBranchId = request('branchId') !== null ?  'branchId' . '='. request('branchId') : '1 = 1';
        $queryEndDate = request('purchaseEndDate') ?  'created_at' . ' <= "' . Carbon::parse(request('purchaseEndDate'))->endOfDay() . '"' : '1 = 1';
        $queryStartDate = request('purchaseStartDate') ?  'created_at' . ' >= "' . Carbon::parse(request('purchaseStartDate'))->startOfDay() .'"' : '1 = 1';

        return $this->hasMany(Purchase::class, 'supplierId', 'id')
            ->whereRaw($queryBranchId)
            ->whereRaw($queryStartDate)
            ->whereRaw($queryEndDate);
    }

    public function duePurchase()
    {
        return $this->purchases()->where('due', '>', 0);
    }

    public function purchaseProducts(): HasManyThrough
    {
        return $this->hasManyThrough(PurchaseProduct::class, Purchase::class , 'supplierId', 'purchaseId');
    }
    /**
     * get the purchaseSummary
     *
     * @return array
     */
    public function purchaseSummary()
    {
//        request()->merge(['supplierId' => $this->id]);
//        $supplierSummary = app(SupplierRepository::class)->calculateSupplierPurchaseDetails(request()->all())->first();

        return [
            "supplierId" => $this->id,
            "totalAmount" => round($this->purchases->sum('totalAmount'),2),
            "totalDue" => round($this->purchases->sum('due'),2),
            "totalPaid" =>  round($this->purchases->sum('paid'),2),
            "totalShippingCost" =>  round($this->purchases->sum('shippingCost'),2),
            "totalDiscount" =>  round($this->purchases->sum('discountAmount'),2),
            "totalTax" => round($this->purchases->sum('taxAmount'),2)
        ];
    }

    public function paymentStatus(): string
    {
        $purchaseSummary = self::purchaseSummary();

        $due = $purchaseSummary['totalDue'];
        $paid = $purchaseSummary['totalPaid'];

        return Payment::paymentStatus($due, $paid);
    }

    /**
     * get the purchaseReturnSummary
     *
     * @return array
     */
    public function purchaseReturnSummary()
    {
        request()->merge(['supplierId' => $this->id]);
        $supplierPurchaseReturnSummary = app(SupplierRepository::class)->calculateSupplierPurchaseReturnDetails(request()->all())->first();

        return [
            "supplierId" => $this->id,
            "totalReturnAmount" => $supplierPurchaseReturnSummary ? round($supplierPurchaseReturnSummary->totalReturnAmount,2) : 0,
            "totalReturnQuantity" => $supplierPurchaseReturnSummary ? round($supplierPurchaseReturnSummary->totalReturnQuantity,4) : 0,
            "totalReturn" => $supplierPurchaseReturnSummary ? $supplierPurchaseReturnSummary->totalReturn : 0
        ];
    }
}
