<?php

namespace App\Http\Requests\Reports;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class SupplierWisePurchaseRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'branchId' => 'numeric',
            'supplierId' => 'numeric',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'withoutPagination' => 'boolean'
        ];
    }
}
