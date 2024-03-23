<?php

namespace App\Http\Requests\Category;

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
            'name' => '',
            'details' => '',
            'code' => '',
            'updatedByUserId' => '',
            'withoutPagination' => 'sometimes|integer',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'query' => 'string',
        ];
    }
}
