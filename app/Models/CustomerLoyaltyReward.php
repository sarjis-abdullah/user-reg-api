<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CustomerLoyaltyReward extends Model
{
    use CommonModelFeatures;

    const ACTION_EARN = 'earn';
    const ACTION_REDEEMED = 'redeemed';

    const TYPE_ORDER = 'order';
    const TYPE_PAYMENT = 'payment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'customerId',
        'loyaltyableId',
        'loyaltyableType',
        'action',
        'points',
        'amount',
        'comment',
        'updatedByUserId',
    ];

    //updating customer points
    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            if($model->action == self::ACTION_EARN) {
                $model->customer()->increment('availableLoyaltyPoints', $model->points);
            } else if($model->action == self::ACTION_REDEEMED){
                $model->customer()->decrement('availableLoyaltyPoints', $model->points);
            }
        });
    }

    /**
     * get the belonging customer
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customerId', 'id');
    }

    /**
     * get the loyaltyable by morphTo
     *
     * @return MorphTo
     */
    public function loyaltyable(): MorphTo
    {
        return $this->morphTo('loyaltyable', 'loyaltyableType', 'loyaltyableId', 'id');
    }

    /**
     * Interact with the payment's paymentable type
     *
     * @return Attribute
     */
    protected function loyaltyableType(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $this->getTypeByClass($value),
            set: fn ($value) => $this->getClassByType($value),
        );
    }

    /**
     * get the relationship class by types
     *
     * @param  string  $type
     * @return string
     */
    protected function getClassByType(string $type): string
    {
        return match ($type) {
            self::TYPE_ORDER => Order::class,
            self::TYPE_PAYMENT => Payment::class,
            default => 'Class',
        };
    }

    /**
     * get the relationship class by types
     *
     * @param  string  $class
     * @return string
     */
    protected function getTypeByClass(string $class): string
    {
        return match ($class) {
            Payment::class => self::TYPE_PAYMENT,
            Order::class => self::TYPE_ORDER,
            default => 'Unknown',
        };
    }
}
