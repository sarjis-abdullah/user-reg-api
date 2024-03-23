<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\Request;
use App\Models\Order;
use App\Models\Payment;

class ChangeOrderStatusRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'in:' . implode(',', Order::getConstantsByPrefix('STATUS_')),
        ];
    }
}
