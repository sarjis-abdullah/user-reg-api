<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\Request;
use App\Models\Employee;

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
            'userId' => 'list:numeric',
            'userRoleId' => 'list:numeric',
            'companyId' => 'list:numeric',
            'branchId' => 'list:numeric',
            'active' => 'sometimes|boolean',
            'title' => '',
            'level' => 'in:' . implode(',', Employee::getConstantsByPrefix('LEVEL_')),
            'updatedByUserId' => 'list:numeric',
            'query' => 'string',
            "startDate" => "date_format:Y-m-d",
            "endDate" => "date_format:Y-m-d",
            'withoutPagination' => 'sometimes|integer',
        ];
    }
}
