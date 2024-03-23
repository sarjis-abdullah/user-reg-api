<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\Request;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;

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
            'companyId' => 'list:string',
            'branchId' => 'list:string',
            'updatedByUserId' => 'list:string',
            'email' => 'string',
            'name' => 'string',
            'address' => 'string',
            'address2' => 'string',
            'city' => 'string',
            'state' => 'string',
            'postCode' => 'string',
            'phone' => 'string',
            'availableLoyaltyPoints' => 'numeric',
            'type' => 'in:'. implode(',', Customer::getConstantsByPrefix('TYPE_')),
            'group' => 'in:'. implode(',', Customer::getConstantsByPrefix('GROUP_')),
            'status' => 'in:'. implode(',', Customer::getConstantsByPrefix('STATUS_')),
            'withoutPagination' => 'sometimes|integer',
            'query' => 'string',
            'isDisabledBranchIdFilter' => 'boolean',
            'orderStartDate' => 'date_format:Y-m-d',
            'orderEndDate' => 'date_format:Y-m-d',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'withSummary'=>'boolean',
            'paymentStatus' => 'in:'.Payment::PAYMENT_STATUS_PAID.','.Payment::PAYMENT_STATUS_UNPAID.','.Payment::PAYMENT_STATUS_PARTIAL,
            'paymentStatusGroup' => 'string'
        ];
    }
}
