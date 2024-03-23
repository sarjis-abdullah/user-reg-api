<?php

namespace App\Http\Requests\Supplier;

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
            'supplierId' => 'required|numeric|exists:suppliers,id',
            'paidAmount' => 'required|numeric',
            'cashFlow' => 'required|in:' . implode(',', Payment::getConstantsByPrefix('CASH_FLOW_')),
            'method' => 'in:' . implode(',', Payment::getConstantsByPrefix('METHOD_')),
            'txnNumber' => 'string',
            'referenceNumber' => 'string',
        ];
    }
}
