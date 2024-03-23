<?php

namespace App\Http\Requests\CompanyModule;

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
            'moduleId' => 'exists:modules,id',
            'activationDate' => 'date_format:Y-m-d',
            'isActive' => 'boolean',
            'updatedByUserId' => 'list:numeric',
        ];
    }
}
