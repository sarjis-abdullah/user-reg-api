<?php

namespace App\Http\Requests\AppSetting;

use App\Http\Requests\Request;
use App\Models\AppSetting;

class SetSettingRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:' . implode(',', AppSetting::getConstantsByPrefix('TYPE_')),
            'branchId' => 'required_if:type,'. AppSetting::TYPE_INVOICE.'|exists:branches,id',
            'settings' => 'required|json',
        ];
    }
}
