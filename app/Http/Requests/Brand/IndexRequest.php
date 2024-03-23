<?php

namespace App\Http\Requests\Brand;

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
            'name' => '',
            'status' => '',
            'origin' => '',
            'details' => '',
            'updatedByUserId' => '',
            'withoutPagination' => 'boolean',
            'query' => 'string',
        ];
    }
}
