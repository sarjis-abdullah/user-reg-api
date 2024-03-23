<?php

namespace App\Http\Requests\Module;

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
            'name' => 'string|min:3|max:255',
            'isActive' => 'boolean',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
