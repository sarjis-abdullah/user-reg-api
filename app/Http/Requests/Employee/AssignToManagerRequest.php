<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\Request;

class AssignToManagerRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'branchId' => 'required|exists:branches,id',
            'level' => 'required|exists:roles,title',
            'employeeId' => 'required|exists:employees,id',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
