<?php

namespace App\Http\Requests\UserProfile;

use App\Models\UserProfile;
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
            'userId' => 'required|exists:users,id',
            'gender' => 'nullable|in:' . implode(',', UserProfile::getConstantsByPrefix('GENDER_')),
            'occupation' => 'nullable|min:3|max:255',
            'homeTown' => 'nullable|max:255',
            'address' => 'nullable|max:255',
            'birthDate' => 'nullable|date_format:Y-m-d',
            'interests' => 'array',
        ];
    }
}
