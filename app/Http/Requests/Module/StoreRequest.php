<?php

namespace App\Http\Requests\Module;

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
        return [
            'createdByUserId' => 'exists:users,id',
            'name' => 'required|string|min:3|max:255',
            'isActive' => 'boolean',
        ];
    }
}
