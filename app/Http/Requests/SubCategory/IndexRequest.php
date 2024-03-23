<?php

namespace App\Http\Requests\SubCategory;

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
            'categoryId' => 'list:numeric',
            'name' => '',
            'code' => '',
            'updatedByUserId' => '',
            'query' => 'string',
            'withoutPagination' => 'sometimes|integer',
        ];
    }
}
