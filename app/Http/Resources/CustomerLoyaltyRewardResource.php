<?php

namespace App\Http\Resources;

use App\Models\CustomerLoyaltyReward;
use Illuminate\Http\Request;

class CustomerLoyaltyRewardResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'=> $this->id,
            'createdByUserId'=> $this->createdByUserId,
            'customerId'=> $this->customerId,
            'customer' => $this->when($this->needToInclude($request, 'clr.customer'), function () {
                return new CustomerResource($this->customer);
            }),
            'loyaltyableId' => $this->loyaltyableId,
            'loyaltyableType' => $this->loyaltyableType,
            'loyaltyable' => $this->when($this->needToInclude($request, 'clr.loyaltyable'), function () {
                return $this->getLoyaltyableResourceByType();
            }),
            'action'=> $this->action,
            'points'=> $this->points,
            'amount'=> $this->amount,
            'comment'=> $this->comment,
            'updatedByUserId'=> $this->updatedByUserId,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
        ];
    }

    /**
     * get the relationship class by types
     *
     */
    private function getLoyaltyableResourceByType()
    {
        return match ($this->loyaltyableType) {
            CustomerLoyaltyReward::TYPE_PAYMENT =>new PaymentResource($this->loyaltyable),
            CustomerLoyaltyReward::TYPE_ORDER =>new OrderResource($this->loyaltyable),
            default => null
        };
    }
}
