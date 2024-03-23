<?php

namespace App\Http\Requests\Income;

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
            'createdByUserId' => 'exists:users,id',
            'branchId' => 'exists:branches,id',
            'categoryId' => 'exists:income_categories,id',
            "amount" => 'required',
            "sourceOfIncome" => 'required',
            "date" => 'required|date_format:Y-m-d',
            "paymentType" => '',
            "notes" => 'sometimes|required',
            'payment' => "",
            'payment.amount' => 'numeric',
            'payment.method' => 'string|in:' . implode(',', Payment::getConstantsByPrefix('METHOD_')),
            'payment.status' => 'string|nullable|in:' . implode(',', Payment::getConstantsByPrefix('status_')),
        ];
    }
}
