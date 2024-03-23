<?php

namespace App\Http\Requests\EcomIntegration;

use App\Http\Requests\Request;
use App\Models\EcomIntegration;

class IndexRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'list:string',
            'createdByUserId' => 'list:string',
            'branchId' => 'list:string',
            'name' => 'in:' . implode(',', EcomIntegration::getConstantsByPrefix('NAME_')),
            'apiUrl' => 'string',
            'apiKey' => 'string',
            'apiSecret' => 'string',
            'updatedByUserId' => 'list:string',
        ];
    }
}
