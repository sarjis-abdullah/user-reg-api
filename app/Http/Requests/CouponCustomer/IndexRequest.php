<?php

namespace App\Http\Requests\CouponCustomer;

use App\Http\Requests\Request;

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
            'id' => 'list:numeric',
            'couponId' => 'list:numeric',
            'customerId' => 'list:numeric',
            'group' => 'string',
            'couponUsage' => 'string',
        ];
    }
}
