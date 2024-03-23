<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PurchaseProductResource extends Resource
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
            'branchId' => $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'pp.branch'), function () {
                return new BranchResource($this->branch);
            }),
            'purchaseId' => $this->purchaseId,
            'purchase' => $this->when($this->needToInclude($request, 'pp.purchase'), function () {
                return new PurchaseResource($this->purchase);
            }),
            'productId' => $this->productId,
            'product' => $this->when($this->needToInclude($request, 'pp.product'), function () {
                return new ProductResource($this->product);
            }),
            'productVariationId' => $this->productVariationId,
            'productVariation' => $this->when($this->needToInclude($request, 'stock.productVariation'), function () {
                return new ProductVariationResource($this->productVariation);
            }),
            'productReturns' => $this->when($this->needToInclude($request, 'pp.productReturns'), function () {
                return PurchaseProductReturnResource::collection($this->productReturns);
            }),
            'date' => $this->date,
            'quantity' => $this->quantity,
            'returnableQuantity' => $this->getReturnableQuantity(),
            'returnedQuantity' => $this->getReturnedQuantity(),
            'sku' => $this->sku,
            'unitCost' => $this->unitCost,
            'discountedUnitCost' => $this->discountedUnitCost,
            'sellingPrice' => $this->sellingPrice,
            'discountAmount' => $this->discountAmount,
            'finalDiscountAmount' => $this->finalDiscountAmount,
            'discountType' => $this->discountType,
            'taxAmount' => $this->taxAmount,
            'totalAmount' => $this->totalAmount,
            'expiredDate' => $this->expiredDate,
            'updatedByUserId' => $this->updatedByUserId,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
