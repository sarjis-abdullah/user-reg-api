<?php

namespace App\Http\Requests\Unit;

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
            'name' => 'list:string',
            'parentId' => 'list:numeric',
            'isFraction' => 'boolean',
            'withoutPagination' => 'sometimes|integer',
            'query' => 'string',
        ];
    }
}
