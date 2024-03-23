<?php

namespace App\Http\Resources;


class QuotationResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'createdByUserId' => $this->createdByUserId,
            'createdByUser' => $this->when($this->needToInclude($request, 'qt.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'branchId' => $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'qt.branch'), function () {
                return new BranchResource($this->branch);
            }),
            'customerId' => $this->customerId,
            'customer' => $this->when($this->needToInclude($request, 'qt.customer'), function () {
                return new CustomerResource($this->customer);
            }),
            'products' =>  $this->products,
            'invoice' => $this->invoice,
            'discount' => $this->discount,
            'shippingCost' => $this->shippingCost,
            'amount' => $this->amount,
            'status' => $this->status,
            'note' => $this->note,
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'qt.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
