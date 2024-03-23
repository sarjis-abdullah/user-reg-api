<?php

namespace App\Http\Requests\Payroll;

use App\Http\Requests\Request;

class StoreRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'createdByUserId' => 'exists:users,id',
            'companyId' => 'required|exists:companies,id',
            'branchId' => 'required|exists:branches,id',
            'employeeId' => 'required|exists:employees,id',
            'date' => 'date_format:Y-m-d',
            'account' => '',
            'amount' => 'required|numeric',
            'method' => '',
            'reference' => '',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
