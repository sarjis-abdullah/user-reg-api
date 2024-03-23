<?php

namespace App\Http\Requests\CompanyModule;

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
            'moduleId' => 'required|exists:modules,id',
            'activationDate' => 'date_format:Y-m-d',
            'isActive' => 'boolean',
            'updatedByUserId' => 'list:numeric',
        ];
    }
}
