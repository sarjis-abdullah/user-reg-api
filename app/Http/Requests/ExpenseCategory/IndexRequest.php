<?php

namespace App\Http\Requests\ExpenseCategory;

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
            'updatedByUserId' => 'list:numeric',
            'branchId' => 'list:numeric',
            'name' => '',
            'description' => '',
            'withoutPagination' => 'sometimes|integer',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'query' => 'string',
        ];
    }
}
