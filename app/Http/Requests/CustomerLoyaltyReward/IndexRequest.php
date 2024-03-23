<?php

namespace App\Http\Requests\CustomerLoyaltyReward;

use App\Http\Requests\Request;
use App\Models\CustomerLoyaltyReward;

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
            'customerId' => 'list:numeric',
            'loyaltyableId' => 'list:numeric',
            'action' => 'in:', implode(',', CustomerLoyaltyReward::getConstantsByPrefix('ACTION_')),
            'type' => 'in:', implode(',', CustomerLoyaltyReward::getConstantsByPrefix('TYPE_')),
            'points' => 'numeric',
            'amount' => 'numeric',
            'updatedByUserId' => 'list:numeric',
        ];
    }
}
