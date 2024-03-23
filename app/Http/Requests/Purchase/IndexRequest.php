<?php

namespace App\Http\Requests\Purchase;

use App\Http\Requests\Request;
use App\Models\Payment;
use App\Models\Purchase;

class IndexRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'list:numeric',
            'status' => 'in:'. implode(',', Purchase::getConstantsByPrefix('STATUS_')),
            'reference' => 'string',
            'gettableDueAmount' => 'numeric',
            'returnedAmount' => 'numeric',
            'date' => 'date_format:Y-m-d',
            'supplierId' => 'list:numeric',
            'paymentStatus' => 'in:'. implode(',', Payment::getConstantsByPrefix('PAYMENT_STATUS_')),
            'branchId' => 'list:numeric',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'query' => 'string',
            'paymentMethod' => 'string',
            'withoutPagination' => 'sometimes|integer',
            'withSummary'=> 'sometimes|boolean',
            'paymentStatusGroup' => 'string',
            'purchaseStatus' => 'in:'. implode(',', Purchase::getConstantsByPrefix('STATUS_')),
            'purchaseStatusGroup' => 'string'
        ];
    }
}
