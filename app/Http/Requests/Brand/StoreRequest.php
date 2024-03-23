<?php

namespace App\Http\Requests\Brand;

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
            'companyId' => 'required|exists:companies,id',
            'name' => 'required|min:3',
            'origin' => 'sometimes|required|min:2',
            'details' => '',
            'status' => '',
        ];
    }
}
