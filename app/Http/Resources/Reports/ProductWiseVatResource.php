<?php

namespace App\Http\Resources\Reports;

use App\Http\Resources\Resource;
use Illuminate\Http\Request;

class ProductWiseVatResource extends Resource
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
            'id' => $this->resource->id,
            'createdByUserId' => $this->resource->createdByUserId,
            'orderId' => $this->resource->orderId,
            'productId' => $this->resource->productId,
            'stockId' => $this->resource->stockId,
            'date' => $this->resource->date,
            'unitPrice' => $this->resource->unitPrice,
            'discountedUnitPrice' => $this->resource->discountedUnitPrice,
            'quantity' => $this->resource->quantity,
            'discount' => $this->resource->discount,
            'discountId' => $this->resource->discountId,
            'tax' => $this->resource->tax,
            'taxId' => $this->resource->taxId,
            'amount' => $this->resource->amount,
            'profitAmount' => $this->resource->profitAmount,
            'grossProfit' => $this->resource->grossProfit,
            'color' => $this->resource->color,
            'size' => $this->resource->size,
            'status' => $this->resource->status,
            'updatedByUserId' => $this->resource->updatedByUserId,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'deleted_at' => $this->resource->deleted_at,
            'productName' => $this->resource->productName,
            'orderBranchId' => $this->resource->orderBranchId,
        ];
    }
}
