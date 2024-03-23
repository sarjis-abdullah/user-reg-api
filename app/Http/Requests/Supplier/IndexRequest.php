<?php

namespace App\Http\Requests\Supplier;

use App\Http\Requests\Request;
use App\Models\Payment;
use App\Models\Supplier;

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
            'updatedByUserId' => 'list:numeric',
            'companyId' => 'list:numeric',
            'supplierId' => 'list:numeric',
            'branchId' => 'list:numeric',
            'name' => '',
            'agencyName' => '',
            'type' => 'in:'. implode(',', Supplier::getConstantsByPrefix('TYPE_')),
            'status' => 'in:'. implode(',', Supplier::getConstantsByPrefix('STATUS_')),
            'email' => '',
            'phone' => '',
            'address' => '',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'purchaseStartDate' => 'date_format:Y-m-d',
            'purchaseEndDate' => 'date_format:Y-m-d',
            'withoutPagination' => 'sometimes|integer',
            'query' => 'string',
            'withSummary'=>'boolean',
            'paymentStatus' => 'in:'.Payment::PAYMENT_STATUS_PAID.','.Payment::PAYMENT_STATUS_UNPAID.','.Payment::PAYMENT_STATUS_PARTIAL,
            'paymentStatusGroup' => 'string'
        ];
    }
}
