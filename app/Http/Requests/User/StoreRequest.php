<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'lastName' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string|regex:/(01)[0-9]{9}/|min:11|unique:users,phone',
            'birthDate' => 'required|date|date_format:Y-m-d|before:today',
            'email' => 'required|email|unique:users,email',
            'occupation' => 'nullable|string',
            'bloodGroup' => 'nullable|string',
            'familyMembers' => 'nullable|string',
            'gender' => 'nullable|string',
            'anniversary' => 'nullable|date|date_format:Y-m-d',
            'hasComplimentaryCard' => 'nullable|boolean',
            'member_id' => 'sometimes|required|string',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
