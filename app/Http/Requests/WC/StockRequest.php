<?php

namespace App\Http\Requests\WC;

use App\Http\Requests\Request;
use App\Models\Order;
use App\Models\Payment;

class StockRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'productId' => 'required|numeric|exists:products,id',
            'sku' => 'required|string',
            'saleQuantity' => 'required|numeric',
        ];
    }
}
