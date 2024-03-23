<?php

namespace App\Http\Requests\CompanyModule;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'list:numeric',
            'createdByUserId' => 'list:numeric',
            'companyId' => 'list:numeric',
            'moduleId' => 'list:numeric',
            'activationDate' => 'date_format:Y-m-d',
            'isActive' => 'boolean',
            'updatedByUserId' => 'list:numeric',
        ];
    }
}
