<?php

namespace App\Http\Requests\PasswordReset;

use App\Http\Requests\Request;

class PasswordResetRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'pin' => 'required|exists:password_resets,pin',
            'password' => 'required|min:6|max:255',
        ];
    }
}
