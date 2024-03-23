<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\Request;
use App\Models\Payment;

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
            'createdByUserId' => 'list:numeric',
            'paymentableId' => 'list:numeric',
            'payType' => 'in:' . implode(',', Payment::getConstantsByPrefix('PAY_TYPE_')),
            'paymentableType' => 'in:' . implode(',', Payment::getConstantsByPrefix('PAYMENT_SOURCE_')),
            'cashFlow' => 'in:' . implode(',', Payment::getConstantsByPrefix('CASH_FLOW_')),
            'method' => 'in:' . implode(',', Payment::getConstantsByPrefix('METHOD_')),
            'amount' => 'numeric',
            'changedAmount' => 'numeric',
            'receivedAmount' => 'numeric',
            'txnNumber' => 'string',
            'date' => 'date_format:Y-m-d',
            'status' => 'in:' . implode(',', Payment::getConstantsByPrefix('STATUS_')),
            'receiveByUserId' => 'list:numeric',
            'updatedByUserId' => 'list:numeric',
            'withoutPagination' => 'sometimes|integer',
        ];
    }
}
