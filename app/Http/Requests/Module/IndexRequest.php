<?php

namespace App\Http\Requests\Module;

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
            'name' => 'string',
            'isActive' => 'boolean',
            'updatedByUserId' => 'list:numeric',
            'withoutPagination' => 'sometimes|integer',
        ];
    }
}
