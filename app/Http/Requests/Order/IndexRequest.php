<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\Request;
use App\Models\Order;
use App\Models\Payment;

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
            'createdByUserId' => 'list:numeric',
            'companyId' => 'list:numeric',
            'branchId' => 'list:numeric',
            'referenceId' => 'list:numeric',
            'salePersonId' => 'list:numeric',
            'customerId' => 'list:numeric',
            'couponId' => 'list:numeric',
            'invoice' => 'list:string',
            'ecomInvoice' => 'list:string',
            'terminal' => 'string',
            'amount' => 'numeric',
            'tax' => 'numeric',
            'discount' => 'numeric',
            'roundOffAmount' => 'numeric',
            'shippingCost' => 'numeric',
            'paid' => 'numeric',
            'due' => 'numeric',
            'deliveryMethod' => 'in:',
            'paymentStatus' => 'in:'. implode(',', Payment::getConstantsByPrefix('PAYMENT_STATUS_')),
            'status' => 'in:'. implode(',', Order::getConstantsByPrefix('STATUS_')),
            'comment' => '',
            'updatedByUserId' => 'list:numeric',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'orderReturnEndDate' => 'date_format:Y-m-d',
            'orderReturnStartDate' => 'date_format:Y-m-d',
            'query' => '',
            'paymentMethod' => 'string',
            'withoutPagination'=> 'sometimes|integer',
            'withSummary'=> 'sometimes|boolean',
            'paymentStatusGroup' => 'string'
        ];
    }
}
