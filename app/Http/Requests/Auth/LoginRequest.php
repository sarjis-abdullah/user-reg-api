<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class LoginRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|exists:users,email',
            'phone' => 'unique:users,phone', //TODO: will have to add a rule for login by either mail or phone
            'password' => 'required',
        ];
    }
}
