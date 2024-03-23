<?php

namespace App\Rules;

use App\Models\Coupon;
use App\Models\CouponCustomer;
use App\Models\Customer;
use App\Repositories\Contracts\CouponRepository;
use App\Repositories\Contracts\CustomerRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class CouponValidationWithSaleAmount implements Rule
{
    /**
     * @var array
     */
    protected $message;
    /**
     * @var
     */
    protected $amount;
    /**
     * @var
     */
    protected $customerId;

    /**
     * Create a new rule instance.
     *
     * @param $amount
     */
    public function __construct($amount, $customerId)
    {
        $this->amount = $amount;
        $this->customerId = $customerId;
        $this->message = '';
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $coupon = app(CouponRepository::class)->findOneBy(['code' => $value]);

        if(!$coupon instanceof Coupon) {
            $this->message = 'The selected coupon code is Invalid!';
            return false;
        }

        if($coupon->to === Coupon::TO_GROUP_CUSTOMER || $coupon->to === Coupon::TO_INDIVIDUAL_CUSTOMER) {
            if(!$this->customerId) {
                $this->message = 'CustomerId is required for this Coupon Code';
                return false;
            }
        }

        //validate the coupon by coupon customer group
        if($coupon->to === Coupon::TO_GROUP_CUSTOMER) {
            $customer = app(CustomerRepository::class)->findOne($this->customerId);

            if (!$customer instanceof Customer) {
                $this->message = 'The selected customer id is invalid.';
                return false;
            }

            $couponCustomer = $coupon->couponCustomers->where('group', $customer->group)->first();

            if(!$couponCustomer instanceof CouponCustomer) {
                $this->message = 'The coupon code is not valid for this customer (different group)!';
                return false;
            }
        } else if ($coupon->to === Coupon::TO_INDIVIDUAL_CUSTOMER) {
            $couponCustomer = $coupon->couponCustomers->where('customerId', $this->customerId)->first();

            if(!$couponCustomer instanceof CouponCustomer) {
                $this->message = 'The coupon code is not valid for this customer!';
                return false;
            }

            if($coupon->maxCouponUsage && ($coupon->maxCouponUsage <= $couponCustomer->couponUsage)) {
                $this->message = 'The coupon code is already been maximum used by this customer!';
                return false;
            }
        }

        if(!empty($coupon->expirationDate) && Carbon::parse($coupon->expirationDate)->endOfDay() >= Carbon::now()) {
            if($coupon->status != Coupon::STATUS_ACTIVE) {
                $this->message = 'The coupon code is not activated yet!';
                return false;
            }

            if(!empty($coupon->startDate) && Carbon::parse($coupon->startDate)->startOfDay() >= Carbon::now()) {
                $this->message = 'The coupon code is not activated yet!';
                return  false;
            }

            if($coupon->minTxnAmount <= $this->amount) {
                return true;
            } else {
                $this->message = 'The total sale amount is not valid for this coupon!';
                return false;
            }
        } else {
            $this->message = 'The coupon code is expired or inactive!';
            return false;
        }
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
