<?php

namespace App\Http\Requests\Discount;

use App\Http\Requests\Request;
use App\Models\Discount;

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
            "createdByUserId" => "exists:users,id",
            "title" => "string",
            "amount" => "required|numeric",
            "type" => 'required|in:' . implode(',', Discount::getConstantsByPrefix('TYPE_')) ,
            "startDate" => "required|date_format:Y-m-d H:i",
            "endDate" => "required|date_format:Y-m-d H:i",
            "note" => "string",
        ];
    }
}
