<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\Request;

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
            'createdByUserId' => 'list:numeric',
            'categoryId' => 'list:numeric',
            'subCategoryId' => 'list:numeric',
            'companyId' => 'list:numeric',
            'brandId' => 'list:numeric',
            'branchId' => 'list:numeric',
            'discountId' => 'list:numeric',
            'isDiscountApplicable' => 'boolean',
            'name' => '',
            'genericName' => '',
            'selfNumber' => '',
            'barcode' => '',
            'tax' => '',
            'description' => '',
            'expiredDate' => 'date_format:Y-m-d',
            'status' => '',
            'updatedByUserId' => 'list:numeric',
            'query' => '',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'quantity' => 'numeric|gte:0',
            'acceptWithoutStock' => 'boolean',
            'alertQuantity' => 'numeric',
            'havingStockAlertQuantity' => 'boolean',
            'sku' => 'string',
            'isStocksArchived'=>'boolean',
            'withoutPagination' => 'sometimes|integer',
            'withSummary' => 'boolean',
            'onlyBundle' => 'boolean',
        ];
    }
}
