<?php

namespace App\Http\Requests\PasswordReset;

use App\Models\PasswordReset;
use App\Http\Requests\Request;
use App\Rules\UserEmailOrPhoneExists;

class GeneratePinRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'emailOrPhone' => ['required', 'max:255', new UserEmailOrPhoneExists()],
            'type' => 'required|in:' . implode(',', PasswordReset::getConstantsByPrefix('TYPE_')),
        ];
    }
}
