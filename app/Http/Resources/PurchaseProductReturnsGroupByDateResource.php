<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PurchaseProductReturnsGroupByDateResource extends Resource
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
            'id' => $this->id,
            'createdByUserId' => $this->createdByUserId,
            'createdByUser' => $this->when($this->needToInclude($request, 'purchase.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'supplierId' => $this->supplierId,
            'supplier' => $this->when($this->needToInclude($request, 'purchase.supplier'), function () {
                return new SupplierResource($this->supplier);
            }),
            'branchId' => $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'purchase.branch'), function () {
                return new BranchResource($this->branch);
            }),
            'purchaseProductReturns' => $this->when($this->needToInclude($request, 'purchase.purchaseProductReturns'), function () {
//                return  $this->purchaseProductReturns->groupBy('date')->toArray(); //TODO create separate resource for this
                return PurchaseProductReturnResource::collection($this->purchaseProductReturns);
            }),
            'date' => $this->date,
            'reference' => $this->reference,
            'paid' => $this->paid,
            'due' => $this->due,
            'totalAmount' => $this->totalAmount,
            'totalReturnAmount' => count($this->purchaseProductReturns) ? $this->purchaseProductReturns->sum('returnAmount') : 0,
            'taxAmount' => $this->taxAmount,
            'discountAmount' => $this->discountAmount,
            'shippingCost' => $this->shippingCost,
            'note' => $this->note,
            'paymentMethods' => $this->paymentMethods(),
            'paymentStatus' => $this->paymentStatus,
            'status' => $this->status,
            'updatedByUserId' => $this->updatedByUserId,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
