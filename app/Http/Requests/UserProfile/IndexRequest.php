<?php

namespace App\Http\Requests\UserProfile;

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
            'id' => 'list:string',
            'createdByUserId' => 'list:numeric',
            'updatedByUserId' => 'list:numeric',
            'userId' => 'numeric',
            'gender' => 'list:string',
            'occupation' => 'list:string',
            'homeTown' => 'list:string',
            'birthDate' => 'list:date_format:Y-m-d',
            'language' => 'list:string'
        ];
    }
}
