<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\Request;
use App\Models\Payment;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'cashFlow' => 'in:' . implode(',', Payment::getConstantsByPrefix('CASH_FLOW_')),
            'method' => 'in:' . implode(',', Payment::getConstantsByPrefix('METHOD_')),
            'amount' => 'numeric',
            'changedAmount' => 'numeric',
            'receivedAmount' => 'numeric',
            'txnNumber' => 'string',
            'referenceNumber' => 'string',
            'date' => 'date_format:Y-m-d',
            'status' => 'in:' . implode(',', Payment::getConstantsByPrefix('STATUS_')),
            'receiveByUserId' => 'exists:users,id',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
