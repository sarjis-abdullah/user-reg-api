<?php

namespace App\Http\Requests\AppSetting;

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
            'type' => '',
            'branchId' => 'numeric',
            'query' => 'string',
        ];
    }
}
