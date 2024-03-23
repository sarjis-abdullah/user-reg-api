<?php

namespace App\Http\Requests\Brand;

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
            'companyId' => 'exists:companies,id',
            'name' => 'min:3',
            'origin' => 'sometimes|required|min:2',
            'details' => '',
            'status' => '',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
