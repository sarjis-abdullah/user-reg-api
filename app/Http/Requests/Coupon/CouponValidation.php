<?php

namespace App\Http\Requests\Coupon;

use App\Http\Requests\Request;
use App\Rules\CouponValidationWithSaleAmount;

class CouponValidation extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'saleAmount' => 'bail|required|numeric',
            'customerId' => 'exists:customers,id',
            'code' =>['required', new CouponValidationWithSaleAmount($this->get('saleAmount'), $this->get('customerId'))],
        ];
    }
}
