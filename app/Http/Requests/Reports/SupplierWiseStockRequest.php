<?php

namespace App\Http\Requests\Reports;

use App\Http\Requests\Request;

class SupplierWiseStockRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'supplierId' => 'numeric',
            'withoutPagination' => 'boolean'
        ];
    }
}
