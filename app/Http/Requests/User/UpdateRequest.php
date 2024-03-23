<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userId = $this->segment(4);
        return $rules = [
            'password' => 'min:6|required_with:current_password|max:255',
            'current_password' => 'required_with:password|max:255',
            'email' => Rule::unique('users')->ignore($userId, 'id'),
            'phone' => [Rule::unique('users')->ignore($userId, 'id')],
            'name' => 'max:255',
            'locale' => 'in:en,bn',
            'isActive' => 'boolean',
            'notificationSeen' => 'boolean',
            'role' => '',
            'role.id' => 'exists:user_roles,id',
            'role.roleId' => 'exists:roles,id',
            'role.oldRoleId' => 'exists:roles,id',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        parent::withValidator($validator);

        $validator->after(function ($validator) {
            if ($this->input('password')) {

                // get User model from route binding
                $user = $this->route('user');

                if (empty($user->password)) {
                    $this->request->add(['password' => $this->input('password')]);
                    $this->request->remove('current_password');
                } else if (Hash::check($this->input('current_password'), $user->password)) {
                    $this->request->add(['password' => $this->input('password')]);
                    $this->request->remove('current_password');
                } else {
                    $validator->errors()->add('current_password', 'Current password doesn\'t match.');
                }
            }
        });
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'password.required_with' => 'New password is required when current password is present.',
        ];
    }

}
