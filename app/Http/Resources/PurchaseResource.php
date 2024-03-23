<?php

namespace App\Http\Resources;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseResource extends Resource
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
            'createdByUser' => $this->when($this->needToInclude($request, 'purchase.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'purchaseProducts' => $this->when($this->needToInclude($request, 'purchase.purchaseProducts'), function () {
                return PurchaseProductResource::collection($this->purchaseProducts);
            }),
            'payments' => $this->when($this->needToInclude($request, 'purchase.payments'), function () {
                return PaymentResource::collection($this->payments);
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
                return PurchaseProductReturnResource::collection($this->purchaseProductReturns);
            }),
            'date' => $this->date ?? $this->created_at,
            'reference' => $this->reference,
            'referenceImage' => $this->when($this->needToInclude($request, 'purchase.referenceImage'), function () {
                return new AttachmentResource($this->referenceImage);
            }),
            'totalAmount' => $this->totalAmount,
            'paid' => $this->paid,
            'due' => $this->getDueAmount(),
            'gettableDueAmount' => $this->gettableDueAmount,
            'returnedAmount' => $this->returnedAmount,
            'totalReturnAmount' => $this->getTotalReturnAmount(),
            'NetPurchaseAmount' => round(($this->totalAmount - $this->returnedAmount),2),
            'NetPaidAmount' => round(($this->paid - $this->gettableDueAmount),2),

            'taxAmount' =>  $this->taxAmount,
            'discountAmount' => $this->discountAmount,
            'totalTaxAmount' => $this->purchaseProducts->sum('totalTaxAmount'),
            'totalDiscountAmount' => $this->purchaseProducts->sum('totalDiscountAmount'),

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
