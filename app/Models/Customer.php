<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\SupplierRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    use CommonModelFeatures;

    const TYPE_REGULAR = 'regular';
    const TYPE_WALK_IN = 'walk-in';
    const TYPE_PREMIUM = 'premium';
    const TYPE_GOLD = 'gold';

    const STATUS_ACTIVE = 'active';
    const STATUS_BANED = 'baned';
    const STATUS_IRREGULAR = 'irregular';

    const GROUP_DIAMOND = 'diamond';
    const GROUP_PLATINUM = 'platinum';
    const GROUP_GOLD = 'gold';
    const GROUP_SILVER = 'silver';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'branchId',
        'name',
        'email',
        'phone',
        'address',
        'address2',
        'city',
        'state',
        'postCode',
        'status',
        'type',
        'group',
        'availableLoyaltyPoints',
        'updatedByUserId',
    ];

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
     * get the orders
     *
     * @return HasMany
     */
    public function orders(): HasMany
    {
        $queryBranchId = request('branchId') !== null ?  'branchId' . '='. request('branchId') : '1 = 1';
        $queryEndDate = request('orderEndDate') ?  'created_at' . '<= "' . Carbon::parse(request('orderEndDate'))->endOfDay() . '"' : '1 = 1';
        $queryStartDate = request('orderStartDate') ?  'created_at' . '>= "' . Carbon::parse(request('orderStartDate'))->startOfDay() . '"' : '1 = 1';

        return $this->hasMany(Order::class, 'customerId', 'id')
            ->whereRaw($queryBranchId)
            ->whereRaw($queryEndDate)
            ->whereRaw($queryStartDate);
    }

    public function dueOrders()
    {
        return $this->orders()->where('due', '>', 0);
    }

    /**
     * get the customer order summary
     *
     * @return array
     */
    public function orderSummary(): array
    {
        request()->merge(['customerId' => $this->id]);
        $customerSummary = app(CustomerRepository::class)->calculateCustomerOrderDetails(request()->all())->first();

        return [
            "customerId" => $this->id,
            "branchId" => request()->get('branchId', null),
            "totalAmount" => $customerSummary ? round($customerSummary->totalAmount, 2) : 0,
            "totalDue" => $customerSummary ? round($customerSummary->totalDue, 2) : 0,
            "totalPaid" => $customerSummary ? round($customerSummary->totalPaid, 2) : 0,
            "totalShippingCost" => $customerSummary ? round($customerSummary->totalShippingCost, 2) : 0,
            "totalDiscount" => $customerSummary ? round($customerSummary->totalDiscount, 2) : 0,
            "totalTax" => $customerSummary ? round($customerSummary->totalTax, 2) : 0
        ];
    }

    /**
     * @return string
     */
    public function paymentStatus(): string
    {
        $orderSummary = self::orderSummary();

        $due = $orderSummary['totalDue'];
        $paid = $orderSummary['totalPaid'];

        return Payment::paymentStatus($due, $paid);
    }

    /**
     * get the orderReturnSummary
     *
     * @return array
     */
    public function orderReturnSummary()
    {
        request()->merge(['customerId' => $this->id]);
        $customerOrderReturnSummary = app(CustomerRepository::class)->calculateCustomerOrderReturnDetails(request()->all())->first();

        return [
            "customerId" => $this->id,
            "branchId" => request()->get('branchId', null),
            "totalReturnAmount" => $customerOrderReturnSummary ? round($customerOrderReturnSummary->totalReturnAmount,2) : 0,
            "totalReturnQuantity" => $customerOrderReturnSummary ? round($customerOrderReturnSummary->totalReturnQuantity,4) : 0,
            "totalReturn" => $customerOrderReturnSummary ? $customerOrderReturnSummary->totalReturn : 0
        ];
    }

    /**
     * @return HasMany
     */
    public function quotations(): HasMany
    {
        return $this->hasMany(Customer::class, 'customerId', 'id');
    }

    /**
     * get all the loyalty rewards records
     *
     * @return HasMany
     */
    public function customerLoyaltyRewards(): HasMany
    {
        return $this->hasMany(CustomerLoyaltyReward::class, 'customerId','id');
    }

    /**
     * get the last entered loyalty reward
     *
     * @return HasOne
     */
    public function lastLoyaltyReward(): HasOne
    {
        return $this->hasOne(CustomerLoyaltyReward::class, 'customerId', 'id')->latestOfMany();
    }

    /**
     * get the last entered loyalty reward
     *
     * @return HasOne
     */
    public function firstLoyaltyReward(): HasOne
    {
        return $this->hasOne(CustomerLoyaltyReward::class, 'customerId', 'id')->oldestOfMany();
    }
}
