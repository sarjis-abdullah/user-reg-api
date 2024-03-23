<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\Request;
use App\Models\Company;

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
            'name' => 'required|min:4',
            'address' => 'sometimes|required|min:10',
            'website' => '',
            'email' => 'sometimes|required|email|unique:companies,email',
            'phone' => 'sometimes|required|unique:companies,phone',
            'type' => 'sometimes|required|string',
            'details' => '',
            'status' => '',
        ];
    }
}
