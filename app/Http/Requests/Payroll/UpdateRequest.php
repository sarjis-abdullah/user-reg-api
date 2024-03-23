<?php

namespace App\Http\Requests\Payroll;

use App\Http\Requests\Request;

class UpdateRequest extends Request
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
            'companyId' => 'exists:companies,id',
            'branchId' => 'exists:branches,id',
            'employeeId' => 'exists:employees,id',
            'date' => 'date_format:Y-m-d',
            'account' => '',
            'amount' => 'numeric',
            'method' => '',
            'reference' => '',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
