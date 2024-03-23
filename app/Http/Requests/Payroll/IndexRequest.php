<?php

namespace App\Http\Requests\Payroll;

use App\Http\Requests\Request;

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
            'companyId' => 'list:numeric',
            'branchId' => 'list:numeric',
            'employeeId' => 'list:numeric',
            'date' => 'date_format:Y-m-d',
            'account' => '',
            'amount' => '',
            'method' => '',
            'reference' => '',
            'updatedByUserId' => 'list:numeric',
            'withoutPagination' => 'sometimes|integer',
        ];
    }
}
