<?php

namespace App\Http\Requests\Discount;

use App\Http\Requests\Request;
use App\Models\Discount;

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
            "title" => "string",
            "amount" => "numeric",
            "type" => 'in:' . implode(',', Discount::getConstantsByPrefix('TYPE_')) ,
            "startDate" => "date_format:Y-m-d H:i",
            "endDate" => "date_format:Y-m-d H:i",
            "note" => "string",
            "updatedByUserId" => "exists:users,id",
        ];
    }
}
