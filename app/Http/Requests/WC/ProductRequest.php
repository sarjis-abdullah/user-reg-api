<?php

namespace App\Http\Requests\WC;

use App\Http\Requests\Request;

class ProductRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'list:numeric',
            'createdByUserId' => 'list:numeric',
            'categoryId' => 'list:numeric',
            'subCategoryId' => 'list:numeric',
            'companyId' => 'list:numeric',
            'brandId' => 'list:numeric',
            'branchId' => 'list:numeric',
            'query' => 'string',
            'acceptWithoutStock' => 'boolean',
            'sku' => 'string',
            'barcode' => 'string',

            'order_by' => 'string',
            'order_direction' => 'in:asc,desc',
            'per_page' => 'numeric|max:100'
        ];
    }
}

