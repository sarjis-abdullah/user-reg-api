<?php

namespace App\Http\Requests\Discount;

use App\Http\Requests\Request;
use App\Models\Discount;

class IndexRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'list:numeric',
            "createdByUserId" => "list:numeric",
            "title" => "string",
            "amount" => "numeric",
            "type" => 'in:' . implode(',', Discount::getConstantsByPrefix('TYPE_')) ,
            "startDate" => "date_format:Y-m-d",
            "endDate" => "date_format:Y-m-d",
            "note" => "string",
            "query" => "string",
            "updatedByUserId" => "list:numeric",
            'withoutPagination' => 'sometimes|integer',
        ];
    }
}
