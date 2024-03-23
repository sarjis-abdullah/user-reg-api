<?php

namespace App\Http\Requests\Coupon;

use App\Http\Requests\Request;
use App\Models\Coupon;
use App\Models\Customer;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $id = $this->segment(4);

        return [
            'title' => 'string|min:5',
            'code' => 'string|min:3|unique:coupons,code,' . $id .',id,deleted_at,NULL',
            'description' => 'string',
            'to' => 'in:' . implode(',', Coupon::getConstantsByPrefix('TO_')),
            'type' => 'in:' . implode(',', Coupon::getConstantsByPrefix('TYPE_')),
            'amount' => 'numeric|gte:0',
            'amountType' => 'in:' . implode(',', Coupon::getConstantsByPrefix('AMOUNT_TYPE_')),
            'minTxnAmount' => 'numeric|gte:0',
            'maxDiscountAmount' => 'numeric|gte:0',
            'usedIn' => 'in:' . implode(',', Coupon::getConstantsByPrefix('USED_IN_')),
            'maxCouponUsage' => 'numeric',
            'startDate' => 'date_format:Y-m-d',
            'expirationDate' => 'date_format:Y-m-d|after_or_equal:startDate',
            'updatedByUserId' => 'exists:users,id',
            'status' => 'in:' . implode(',', Coupon::getConstantsByPrefix('STATUS_')),

            'groups' => 'required_if:to,'. Coupon::TO_GROUP_CUSTOMER . '|array|min:1',
            'groups.*' => 'required|string|distinct|in:' . implode(',', Customer::getConstantsByPrefix('GROUP_')),
            'customerIds' => 'required_if:to,'. Coupon::TO_INDIVIDUAL_CUSTOMER . '|min:1',
            'customerIds.*' => 'required|distinct|exists:customers,id',
        ];
    }
}
