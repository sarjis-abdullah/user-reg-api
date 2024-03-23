<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\Request;
use App\Models\Company;

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
            'createdByUserId' => 'exists:users,id',
            'name' => 'min:4',
            'address' => 'sometimes|min:10',
            'website' => '',
            'email' => 'sometimes|email|unique:companies,email',
            'phone' => 'sometimes|unique:companies,phone',
            'type' => 'string',
            'details' => '',
            'status' => '',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
