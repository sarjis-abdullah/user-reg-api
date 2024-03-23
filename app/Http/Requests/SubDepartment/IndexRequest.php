<?php

namespace App\Http\Requests\SubDepartment;

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
            'department_id' => 'list:numeric',
            'name' => '',
            'updatedByUserId' => 'list:numeric',
            'query' => 'string',
            'withoutPagination' => 'sometimes|integer',
        ];
    }
}
