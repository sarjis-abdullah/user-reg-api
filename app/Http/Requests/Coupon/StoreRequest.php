<?php

namespace App\Http\Requests\Coupon;

use App\Http\Requests\Request;
use App\Models\Coupon;
use App\Models\Customer;

class StoreRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'createdByUserId' => 'exists:users,id',
            'title' => 'required|string|min:5',
            'code' => 'required|string|unique:coupons,code,NULL,id,deleted_at,NULL',
            'description' => 'string',
            'to' => 'required|in:' . implode(',', Coupon::getConstantsByPrefix('TO_')),
            'type' => 'required|in:' . implode(',', Coupon::getConstantsByPrefix('TYPE_')),
            'amount' => 'required|numeric|gte:0',
            'amountType' => 'required|in:' . implode(',', Coupon::getConstantsByPrefix('AMOUNT_TYPE_')),
            'minTxnAmount' => 'required|numeric|gte:0',
            'maxDiscountAmount' => 'required|numeric|gte:0',
            'usedIn' => 'in:' . implode(',', Coupon::getConstantsByPrefix('USED_IN_')),
            'maxCouponUsage' => 'numeric',
            'startDate' => 'required|date_format:Y-m-d',
            'expirationDate' => 'required|date_format:Y-m-d|after_or_equal:startDate',
            'status' => 'required|in:' . implode(',', Coupon::getConstantsByPrefix('STATUS_')),

            'groups' => 'required_if:to,'. Coupon::TO_GROUP_CUSTOMER . '|array|min:1',
            'groups.*' => 'required|string|distinct|in:' . implode(',', Customer::getConstantsByPrefix('GROUP_')),
            'customerIds' => 'required_if:to,'. Coupon::TO_INDIVIDUAL_CUSTOMER . '|min:1',
            'customerIds.*' => 'required|distinct|exists:customers,id',
        ];
    }
}
