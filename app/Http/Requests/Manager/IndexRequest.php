<?php

namespace App\Http\Requests\Manager;

use App\Http\Requests\Request;
use App\Models\Manager;

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
            'title' => '',
            'level' => 'string',
            'updatedByUserId' => 'list:numeric',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'query' => 'string',
            'withoutPagination' => 'sometimes|integer',
        ];
    }
}
