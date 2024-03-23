<?php

namespace App\Http\Requests\Attachment;

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
        return $rules = [
            'id'    => 'list:string',
            'resourceId' => 'list:string',
            'type' => 'list:string',
            'variation' => 'list:string'
        ];
    }

}
