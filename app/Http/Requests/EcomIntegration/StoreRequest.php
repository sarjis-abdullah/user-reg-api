<?php

namespace App\Http\Requests\EcomIntegration;

use App\Http\Requests\Request;
use App\Models\EcomIntegration;
use App\Rules\ValidateEcomIntegrationApiUrl;

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
            'branchId' => 'required|exists:branches,id|unique:ecom_integrations,branchId',
            'name' => 'required|unique:ecom_integrations,name|in:' . implode(',', EcomIntegration::getConstantsByPrefix('NAME_')),
            'apiUrl' => ['required', 'string', 'max:256', new ValidateEcomIntegrationApiUrl($this->get('apiKey'), $this->get('apiSecret'))],
            'apiKey' => 'required_with:apiUrl|string|max:256',
            'apiSecret' => 'required_with:apiUrl|string|max:256'
        ];
    }
}
