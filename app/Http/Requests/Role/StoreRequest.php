<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\Request;

class StoreRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            'createdByUserId' => 'exists:users,id',
            'title' => 'required|unique:roles,title|min:3|max:255',
            'type' => 'required|min:3|max:255',
        ];
    }
}
