<?php

namespace App\Http\Requests\EcomIntegration;

use App\Http\Requests\Request;
use App\Models\EcomIntegration;
use App\Rules\ValidateEcomIntegrationApiUrl;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $userId = $this->segment(4);

        return [
            'branchId' => 'required|exists:branches,id|unique:ecom_integrations,branchId,' . $userId,
            'name' => 'in:' . implode(',', EcomIntegration::getConstantsByPrefix('NAME_')) . '|unique:ecom_integrations,name,' . $userId . ',id',
            'apiUrl' => ['required', 'string', 'max:256', new ValidateEcomIntegrationApiUrl($this->get('apiKey'), $this->get('apiSecret'))],
            'apiKey' => 'required_with:apiUrl|string|max:256',
            'apiSecret' => 'required_with:apiUrl|string|max:256',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
