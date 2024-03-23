<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PurchaseProductReturnResource extends Resource
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
            'createdByUserId' =>  $this->createdByUserId,
            'createdByUser' => $this->when($this->needToInclude($request, 'ppr.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'branchId' =>  $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'ppr.branch'), function () {
                return new BranchResource($this->branch);
            }),
            'purchaseId' =>  $this->purchaseId,
            'purchase' => $this->when($this->needToInclude($request, 'ppr.purchase'), function () {
                return new PurchaseResource($this->purchase());
            }),
            'purchaseProductId' =>  $this->purchaseProductId,
            'purchaseProductName' =>  $this->purchaseProduct && $this->purchaseProduct->product ? $this->purchaseProduct->product->name : '',
            'purchaseProduct' => $this->when($this->needToInclude($request, 'ppr.purchaseProduct'), function () {
                return new PurchaseProductResource($this->purchaseProduct);
            }),
            'date' =>  $this->date,
            'comment' => $this->comment,
            'returnAmount' => $this->returnAmount,
            'quantity' => $this->quantity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
