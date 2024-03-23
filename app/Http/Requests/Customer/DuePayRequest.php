<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\Request;
use App\Models\Payment;

class DuePayRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customerId' => 'required|numeric|exists:customers,id',
            'paidAmount' => 'required|numeric',
            'cashFlow' => 'required|in:' . implode(',', Payment::getConstantsByPrefix('CASH_FLOW_')),
            'method' => 'in:' . implode(',', Payment::getConstantsByPrefix('METHOD_')),
            'txnNumber' => 'string',
            'referenceNumber' => 'string',
        ];
    }
}
