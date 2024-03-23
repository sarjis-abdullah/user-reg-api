<?php

namespace App\Rules;

use App\Models\Order;
use App\Models\Purchase;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\PurchaseRepository;
use Illuminate\Contracts\Validation\Rule;

class PaymentResourceAmountValidate implements Rule
{
    protected $amount;
    /**
     * @var array
     */
    protected $message;
    /**
     * @var
     */
    protected $paymentableType;

    /**
     * Create a new rule instance.
     *
     * @param $amount
     */
    public function __construct($amount, $paymentableType)
    {
        $this->amount = $amount;
        $this->paymentableType = $paymentableType;
        $this->message = '';
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($this->paymentableType == Purchase::class) {
            $purchaseRepo = app(PurchaseRepository::class);

            $purchase = $purchaseRepo->findOne($value);

            if(!$purchase instanceof Purchase) {
                $this->message = 'The selected purchase id is invalid.';
                return false;
            }

            if($purchase->paid >= $purchase->totalAmount) {
                $this->message = 'This Purchase has less or no amount left to pay';
                return false;
            }

        } else if($this->paymentableType == Order::class) {
            $orderRepo = app(OrderRepository::class);

            $order = $orderRepo->findOne($value);

            if(!$order instanceof Order) {
                $this->message = 'The selected order id is invalid.';
                return false;
            }

            if($this->amount > $order->due) {
                $this->message = 'This Order has less amount to pay then the current paying amount';
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
