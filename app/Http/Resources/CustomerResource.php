<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CustomerResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'createdByUserId' => $this->createdByUserId,
            'createdByUser' => $this->when($this->needToInclude($request, 'customer.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'branchId' => $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'customer.branch'), function () {
                return new BranchResource($this->branch);
            }),
            'loyaltyRewards' => $this->when($this->needToInclude($request, 'customer.loyaltyRewards'), function () {
                return CustomerLoyaltyRewardResource::collection($this->customerLoyaltyRewards);
            }),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'address2' => $this->address2,
            'city' => $this->city,
            'state' => $this->state,
            'postCode' => $this->postCode,
            'status' => $this->status,
            'type' => $this->type,
            'group' => $this->group,
            'availableLoyaltyPoints' => $this->availableLoyaltyPoints,
            'paymentStatus' => $this->paymentStatus(),
            'orders' => $this->when($this->needToInclude($request, 'customer.orders'), function () {
                return OrderResource::collection($this->orders);
            }),
            'orderSummary' => $this->when($this->needToInclude($request, 'customer.orderSummary'), function () {
                return new CustomerOrderSummaryResource($this->orderSummary());
            }),
            'orderReturnSummary' => $this->when($this->needToInclude($request, 'customer.orderReturnSummary'), function () {
                return new CustomerOrderReturnSummaryResource($this->orderReturnSummary());
            }),
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'customer.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
