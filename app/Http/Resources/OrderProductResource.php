<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class OrderProductResource extends Resource
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
            'createdByUser' => $this->when($this->needToInclude($request, 'op.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'productId' => $this->productId,
            'product' => $this->when($this->needToInclude($request, 'op.product'), function () {
                return new ProductResource($this->product);
            }),
            'orderId' => $this->orderId,
            'order' => $this->when($this->needToInclude($request, 'op.order'), function () {
                return new OrderResource($this->order);
            }),
            'stockId' => $this->stockId,
            'stock' => $this->when($this->needToInclude($request, 'op.stock'), function () {
                return new StockResource($this->stock);
            }),
            'productReturns' => $this->when($this->needToInclude($request, 'op.productReturns'), function () {
                return OrderProductReturnResource::collection($this->productReturns);
            }),
            'date' => $this->date,
            'unitPrice' => $this->unitPrice,
            'discountedUnitPrice' => $this->discountedUnitPrice,
            'quantity' => $this->quantity,
            'returnableQuantity' => $this->quantity - $this->productReturns->sum('quantity'),
            'returnedQuantity' => $this->getReturnedQuantity(),
            'amount' => $this->amount,
            'profitAmount' => $this->profitAmount,
            'grossProfit' => $this->grossProfit,
            'tax' => $this->tax,
            'unitTax' => $this->when($this->needToInclude($request, 'op.tax'), function () {
                return new TaxResource($this->getTax);
            }),
            'unitDiscount' => $this->when($this->needToInclude($request, 'op.discount'), function () {
                return new DiscountResource($this->getDiscount);
            }),
            'taxId' => $this->taxId,
            'discount' => $this->discount,
            'discountId' => $this->discountId,
            'size' => $this->size,
            'color' => $this->color,
            'status' => $this->status,
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'op.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
