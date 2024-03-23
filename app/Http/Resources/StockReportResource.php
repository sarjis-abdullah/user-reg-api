<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class StockReportResource extends Resource
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
            'branchId' => $this->branchId,
            'branch' => new BranchResource($this->branch),
            'productVariationId' => $this->productVariationId,
            'sku' => $this->sku,
            'quantity' => $this->quantity,
            'orderProductQuantity' => $this->orderProducts->sum('quantity'),
            'orderReturnedQuantity' => $this->productReturned->sum('quantity'),
            'totalSoldQuantity' =>  ($this->orderProducts->sum('quantity') - $this->productReturned->sum('quantity')),
            'alertQuantity' => $this->alertQuantity,
            'unitCost' => $this->unitCost,
            'stockPrice'=> round(($this->quantity * $this->unitCost),2),
            'stockSalePrice'=> round(($this->quantity * $this->unitPrice),2),
            'unitPrice' => $this->unitPrice,
            'unitProfit' => $this->unitProfit,
            'stockProfit' => $this->stockProfit,
            'discountAmount' => $this->discountAmount,
            'discountedUnitCost' => $this->discountedUnitCost,
            'existingUnitCost' => $this->existingUnitCost,
            'existingDiscount' => $this->existingDiscount,
            'discountType' => $this->discountType,
            'grossProfit' => $this->grossProfit,
            'expiredDate' => $this->expiredDate,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
