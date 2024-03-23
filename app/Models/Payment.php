<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use CommonModelFeatures;

    const METHOD_COD = 'cod';
    const METHOD_CARD = 'card';
    const METHOD_NAGAD = 'nagad';
    const METHOD_BKASH = 'bkash';
    const METHOD_UPAY = 'upay';
    const METHOD_ROCKET = 'rocket';
    const METHOD_TAP = 'tap';
    const METHOD_BANK_TRANSFER = 'bank-transfer';
    const METHOD_CHEQUE = 'cheque';
    const METHOD_CASH = 'cash';
    const METHOD_DUE = 'due';
    const METHOD_LOYALTY_REWARD = 'loyalty-reward';
    const METHOD_OTHERS = 'others';

    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_DECLINED = 'declined';

    const CASH_FLOW_IN = 'in';
    const CASH_FLOW_OUT = 'out';

    const PAYMENT_STATUS_PAID = 'paid'   ;
    const PAYMENT_STATUS_UNPAID = 'unpaid';
    const PAYMENT_STATUS_PARTIAL = 'partial';

    const PAYMENT_TYPE_DEBIT = 'debit';
    const PAYMENT_TYPE_CREDIT = 'credit';

    const PAY_TYPE_ORDER = 'order';
    const PAY_TYPE_ORDER_EXCHANGE = 'order-exchange';
    const PAY_TYPE_ORDER_DUE = 'order-due';
    const PAY_TYPE_PURCHASE = 'purchase';
    const PAY_TYPE_PURCHASE_DUE = 'purchase-due';
    const PAY_TYPE_INCOME = 'income';
    const PAY_TYPE_EXPENSE = 'expense';

    const PAYMENT_SOURCE_INCOME = 'income';
    const PAYMENT_SOURCE_EXPENSE = 'expense';
    const PAYMENT_SOURCE_ORDER = 'order';
    const PAYMENT_SOURCE_ORDER_DUE = 'order-due';
    const PAYMENT_SOURCE_ORDER_PRODUCT_RETURN = 'order-product-return';
    const PAYMENT_SOURCE_PURCHASE = 'purchase';
    const PAYMENT_SOURCE_PURCHASE_DUE = 'purchase-due';
    const PAYMENT_SOURCE_PURCHASE_PRODUCT_RETURN = 'purchase-product-return';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'paymentableId',
        'paymentableType',
        'payType',
        'cashFlow',
        'method',
        'amount',
        'txnNumber',
        'referenceNumber',
        'receivedAmount',
        'changedAmount',
        'date',
        'status',
        'receivedByUserId',
        'updatedByUserId',
    ];

    /**
     * get the paymentable by morphTo
     *
     * @return MorphTo
     */
    public function paymentable(): MorphTo
    {
        return $this->morphTo('paymentable', 'paymentableType', 'paymentableId', 'id');
    }

    /**
     * Interact with the payment's paymentable type
     *
     * @return Attribute
     */
    protected function paymentableType(): Attribute
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
            self::PAYMENT_SOURCE_INCOME => Income::class,
            self::PAYMENT_SOURCE_EXPENSE => Expense::class,
            self::PAYMENT_SOURCE_ORDER => Order::class,
            self::PAYMENT_SOURCE_ORDER_DUE => Order::class,
            self::PAYMENT_SOURCE_ORDER_PRODUCT_RETURN => OrderProductReturn::class,
            self::PAYMENT_SOURCE_PURCHASE => Purchase::class,
            self::PAYMENT_SOURCE_PURCHASE_DUE => Purchase::class,
            self::PAYMENT_SOURCE_PURCHASE_PRODUCT_RETURN => PurchaseProductReturn::class,
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
            Income::class => self::PAYMENT_SOURCE_INCOME,
            Expense::class => self::PAYMENT_SOURCE_EXPENSE,
            Order::class => self::PAYMENT_SOURCE_ORDER,
            Order::class => self::PAYMENT_SOURCE_ORDER_DUE,
            OrderProductReturn::class => self::PAYMENT_SOURCE_ORDER_PRODUCT_RETURN,
            Purchase::class => self::PAYMENT_SOURCE_PURCHASE,
            Purchase::class => self::PAYMENT_SOURCE_PURCHASE_DUE,
            PurchaseProductReturn::class => self::PAYMENT_SOURCE_PURCHASE_PRODUCT_RETURN,
            default => 'Unknown',
        };
    }

    /**
     * get the order paymentStatus
     *
     */
    public static function paymentStatus($due, $paid): string
    {
        if($due > 0 && $paid == 0) {
            return self::PAYMENT_STATUS_UNPAID;
        } else if($due > 0 && $paid > 0) {
            return self::PAYMENT_STATUS_PARTIAL;
        } else {
            return self::PAYMENT_STATUS_PAID;
        }
    }

    /**
     * @return string[]
     */
    public static function referenceNumberRequiredAblePaymentMethod(): array
    {
        return [self::METHOD_BKASH, self::METHOD_CARD, self::METHOD_NAGAD, self::METHOD_ROCKET, self::METHOD_UPAY, self::METHOD_TAP, self::METHOD_BANK_TRANSFER, self::METHOD_CHEQUE];
    }
}
