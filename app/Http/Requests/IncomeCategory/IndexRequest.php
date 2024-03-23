<?php

namespace App\Http\Requests\IncomeCategory;

use App\Http\Requests\Request;

class IndexRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|required',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'query' => 'string',
            'withoutPagination' => 'sometimes|integer',
        ];
    }
}
