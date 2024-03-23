<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\Request;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'min:3',
            'details' => '',
            'code' => '',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
