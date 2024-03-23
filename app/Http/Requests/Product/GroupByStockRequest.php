<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class GroupByStockRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'query' => 'string',
            'branchId' => 'list:numeric',
            'havingStockAlertQuantity' => 'boolean',
            'isGroupByMostSale'=> 'boolean',
            'withoutPagination'=> 'boolean',
            'isExactBarcodeSearch'=> 'boolean'
        ];
    }
}
