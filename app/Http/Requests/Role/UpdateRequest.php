<?php

namespace App\Http\Requests\Role;

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
        return $rules = [
            'title' => 'min:3|max:255',
            'type' => 'min:3|max:255',
            'updatedByUserId' => 'exists:users,id',
        ];
    }

}
