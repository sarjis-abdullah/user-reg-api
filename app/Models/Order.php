<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use App\Repositories\Contracts\CustomerLoyaltyRewardRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Order extends Model
{
    use CommonModelFeatures;

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_ON_HOLD = 'on-hold';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_FAILED = 'failed';
    const STATUS_TRASH = 'trash';

    const STATUS_DELIVERED = 'delivered';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_ORDERED = 'ordered';
    const STATUS_RETURNED = 'returned';

    const CURRENT_INVOICE_PREFIX = 'RT';

    const DELIVERY_METHOD_ON_SPORT = 'on-spot';
    const DELIVERY_METHOD_ON_WEB = 'home';
    const DELIVERY_METHOD_TRANSFER = 'transfer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'referenceOrderId',

        'createdByUserId',
        'companyId',
        'branchId',
        'salePersonId',
        'customerId',
        'referenceId',
        'date',
        'terminal',
        'invoice',
        'tax',
        'roundOffAmount',
        'discount',
        'shippingCost',
        'amount',
        'profitAmount',
        'grossProfit',
        'paid',
        'due',
        'deliveryMethod',
        'paymentStatus',
        'status',
        'comment',
        'ecomInvoice',
        'orderUrl',
        'shipping',
        'updatedByUserId',
        'couponId',
    ];

    protected $casts = [
        'shipping' => 'json'
    ];

    //generating auto invoice number
    public static function booted()
    {
        parent::booted();

        $appSettings = AppSetting::all();

        self::created(function ($model) use($appSettings) {
            $prefix = '';
            //TODO: costly, may need to pass prefix from frontend if required
            $invoiceSettings = $appSettings->where('branchId', request('branchId'))->where('type', AppSetting::TYPE_INVOICE)->first();

            if($invoiceSettings instanceof AppSetting) {
                $prefix = $invoiceSettings->settings && $invoiceSettings->settings->invoicePrefix ? $invoiceSettings->settings->invoicePrefix : self::CURRENT_INVOICE_PREFIX;
            }

            //TODO: review the str_pad in near future
            $model->invoice = $prefix . str_pad($model->id, strlen($model->id) >= 6 ? 9 : 6, '0', STR_PAD_LEFT);
            $model->save();
        });

        self::created(function ($model) use ($appSettings) {
            $loyaltyRewardSettings =  $appSettings->where('type', AppSetting::TYPE_LOYALTY_REWARD)->first();

            if ($loyaltyRewardSettings && $loyaltyRewardSettings->settings && $loyaltyRewardSettings->settings->pointPerAmount) {
                $pointsEarnInThisOrder = ceil($model->amount / (int) $loyaltyRewardSettings->settings->pointPerAmount);

                app(CustomerLoyaltyRewardRepository::class)->save([
                    'customerId' => $model->customerId,
                    'loyaltyableId' => $model->id,
                    'loyaltyableType' => CustomerLoyaltyReward::TYPE_ORDER,
                    'action' => CustomerLoyaltyReward::ACTION_EARN,
                    'points' => $pointsEarnInThisOrder,
                    'amount' => $model->amount,
                ]);
            }
        });
    }

    /**
     * get the coupon
     *
     * @return HasMany
     */
    public function orderLogs(): HasMany
    {
        return $this->hasMany(OrderLog::class, 'orderId', 'id');
    }

    /**
     * get the coupon
     *
     * @return HasOne
     */
    public function coupon(): HasOne
    {
        return $this->hasOne(Coupon::class, 'id', 'couponId');
    }

    /**
     * get the company
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'companyId', 'id');
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

    /**
     * get the salePerson
     *
     * @return BelongsTo
     */
    public function salePerson(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'salePersonId', 'id');
    }

    /**
     * get the order products
     *
     * @return HasMany
     */
    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'orderId', 'id');
    }

    /**
     * Get the comments.
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable','paymentableType', 'paymentableId', 'id');
    }

    /**
     * get the order paymentMethods
     *
     */
    public function paymentMethods(): string
    {
        $methods = $this->payments()->pluck('method')->implode(',');
        return implode(',', array_unique(explode(',', $methods)));
    }

    /**
     * get the invoice image
     *
     * @return HasOne
     */
    public function invoiceImage(): HasOne
    {
        return $this->hasOne(Attachment::class, 'resourceId', 'id')
            ->where('type', Attachment::ATTACHMENT_TYPE_INVOICE)
            ->latest();
    }

    /**
     * get the order product returns
     *
     */
    /*public function orderProductReturns(): HasManyThrough
    {
        return $this->hasManyThrough(OrderProductReturn::class, OrderProduct::class, 'orderId', 'orderProductId')
            ->when(request()->filled('orderReturnEndDate') && request()->filled('orderReturnStartDate'), function ($query){
                $query->whereDate('order_product_returns.created_at', '<=', request()->get('orderReturnEndDate'))
                    ->whereDate('order_product_returns.created_at', '>=', request()->get('orderReturnStartDate'));
            });
    }*/

    /**
     * @return HasMany|mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function orderProductReturns()
    {
        return $this->hasMany(OrderProductReturn::class,  'orderId', 'id')
            ->when(request()->filled('orderReturnEndDate') && request()->filled('orderReturnStartDate'), function ($query){
                $query->whereDate('order_product_returns.created_at', '<=', request()->get('orderReturnEndDate'))
                    ->whereDate('order_product_returns.created_at', '>=', request()->get('orderReturnStartDate'));
            });
    }


    /**
     * @return HasManyThrough
     */
    public function orderReturnAmount(): HasManyThrough
    {
        return $this->hasManyThrough(OrderProductReturn::class, OrderProduct::class, 'orderId', 'orderProductId')
            ->select(DB::raw('sum(order_product_returns.returnAmount * order_product_returns.quantity) as totalReturnAmount'));
    }

    /**
     * @return float|int|mixed
     */
    public function getTotalReturnAmount()
    {
        return count($this->orderProductReturns) ? $this->orderProductReturns->sum('returnAmount') : 0.0;
    }

    /**
     * @return float|int|mixed
     */
    public function getTotalReturnProfit()
    {
        return count($this->orderProductReturns) ? ($this->orderProductReturns->sum('profitAmount') - $this->orderProductReturns->sum('discountAmount')) : 0.0;
    }

    public function totalRawProductPrice()
    {
        return $this->orderProducts->map(function ($item){
            return ($item->unitPrice * $item->quantity);
        })->first();
    }

    /**
     * @return float|int|mixed
     */
    public function getDueAmount()
    {
        if($this->due > $this->getTotalReturnAmount()) {
            $due = $this->due - $this->getTotalReturnAmount();
        } else {
            $due = $this->due;
        }
        return round($due, 2);
    }

    /**
     * Get the comments.
     */
    public function customerLoyaltyReward(): MorphOne
    {
        return $this->morphOne(CustomerLoyaltyReward::class, 'loyaltyable', 'loyaltyableType', 'loyaltyableId');
    }

    /**
     * @return array
     */
    public function loyaltyReward(): array
    {
        if($this->customerLoyaltyReward) {
            return [
                'rewardEarn' => $this->customerLoyaltyReward->action == CustomerLoyaltyReward::ACTION_EARN ? $this->customerLoyaltyReward->points : 0,
                'previousPoints' => $this->customer->availableLoyaltyPoints - $this->customerLoyaltyReward->points,
                'currentPoints' => $this->customer->availableLoyaltyPoints,
            ];
        }

        return [];
    }
}
