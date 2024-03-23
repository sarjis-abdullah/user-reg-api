<?php

namespace App\Http\Requests\Tax;

use App\Models\Tax;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            "title" => "string",
            "amount" => "numeric",
            "type" => 'in:' . Tax::TYPE_PERCENTAGE ,
            "action" => 'in:' . implode(',', Tax::getConstantsByPrefix('ACTION_')) ,
            "notes" => "string",
            "updatedByUserId" => "exists:users,id",
        ];
    }
}
