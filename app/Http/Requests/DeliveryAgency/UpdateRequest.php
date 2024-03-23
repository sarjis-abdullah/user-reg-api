<?php

namespace App\Http\Requests\DeliveryAgency;

use App\Http\Requests\Request;
use App\Models\StockTransfer;

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
            'name' => 'required',
            'address' => '',
            'phone' => '',
            'email' => '',
            'contactPerson' => '',
        ];
    }
}
