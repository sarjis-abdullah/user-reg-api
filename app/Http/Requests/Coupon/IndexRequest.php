<?php

namespace App\Http\Requests\Coupon;

use App\Http\Requests\Request;
use App\Models\Coupon;
use App\Models\Customer;

class IndexRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'list:string',
            'createdByUserId' => 'list:string',
            'title' => 'string',
            'code' => 'string',
            'description' => '',
            'to' => 'in:' . implode(',', Coupon::getConstantsByPrefix('TO_')),
            'type' => 'in:' . implode(',', Coupon::getConstantsByPrefix('TYPE_')),
            'amount' => 'numeric',
            'amountType' => 'in:' . implode(',', Coupon::getConstantsByPrefix('AMOUNT_TYPE_')),
            'minTxnAmount' => 'numeric',
            'maxDiscountAmount' => 'numeric',
            'usedIn' => 'in:' . implode(',', Coupon::getConstantsByPrefix('USED_IN_')),
            'maxCouponUsage' => 'numeric',
            'startDate' => 'date_format:Y-m-d',
            'expirationDate' => 'date_format:Y-m-d',
            'updatedByUserId' => 'list:string',
            'status' => 'in:' . implode(',', Coupon::getConstantsByPrefix('STATUS_')),
            'groups' => 'array|in:' . implode(',', Customer::getConstantsByPrefix('GROUP_')),
            'customerIds' => 'array'
        ];
    }
}
