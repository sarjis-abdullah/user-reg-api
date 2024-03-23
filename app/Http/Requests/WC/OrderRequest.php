<?php

namespace App\Http\Requests\WC;

use App\Http\Requests\Request;
use App\Models\Order;
use App\Models\Payment;

class OrderRequest extends Request
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
            'createdByUserId' => 'list:numeric',
            'companyId' => 'list:numeric',
            'branchId' => 'list:numeric',
            'referenceId' => 'list:numeric',
            'salePersonId' => 'list:numeric',
            'customerId' => 'list:numeric',
            'couponId' => 'list:numeric',
            'invoice' => 'list:string',
            'terminal' => 'string',
            'amount' => 'numeric',
            'tax' => 'numeric',
            'discount' => 'numeric',
            'roundOffAmount' => 'numeric',
            'shippingCost' => 'numeric',
            'paid' => 'numeric',
            'due' => 'numeric',
            'deliveryMethod' => 'in:'. implode(',', Order::getConstantsByPrefix('DELIVERY_METHOD_')),
            'paymentStatus' => 'in:'. implode(',', Payment::getConstantsByPrefix('PAYMENT_STATUS_')),
            'status' => 'in:'. implode(',', Order::getConstantsByPrefix('STATUS_')),
            'updatedByUserId' => 'list:numeric',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'query' => '',

            'order_by' => 'string',
            'order_direction' => 'in:asc,desc',
            'per_page' => 'numeric|max:100'
        ];
    }
}
