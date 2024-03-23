<?php

namespace App\Http\Requests\UserProfile;

use App\Models\UserProfile;
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
            'userId' => 'exists:users,id',
            'gender' => 'nullable|in:' . implode(',', UserProfile::getConstantsByPrefix('GENDER_')),
            'occupation' => 'nullable|min:3|max:255',
            'homeTown' => 'min:3|max:255',
            'address' => 'nullable|max:255',
            'birthDate' => 'nullable|date_format:Y-m-d',
            'interests' => '',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
