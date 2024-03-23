<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class StockResource extends Resource
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
            'createdByUser' => $this->when($this->needToInclude($request, 'stock.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'archivedByUserId' => $this->archivedByUserId,
            'archivedByUser' => $this->when($this->needToInclude($request, 'stock.archivedByUser'), function () {
                return new UserResource($this->archivedByUser);
            }),
            'branchId' => $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'stock.branch'), function () {
                return new BranchResource($this->branch);
            }),
            'productId' => $this->productId,
            'product' => $this->when($this->needToInclude($request, 'stock.product'), function () {
                return new ProductResource($this->product);
            }),
            'productVariationId' => $this->productVariationId,
            'productVariation' =>  new ProductVariationResource($this->productVariation),
            'createdFromResourceId' =>  $this->createdFromResourceId,

            'sku' => $this->sku,
            'quantity' => $this->quantity,
            'purchaseQuantity' => $this->purchaseQuantity,
            'orderProductQuantity' => $this->when($this->needToInclude($request, 'stock.orderProductQuantity'), function () {
                return $this->orderProducts->sum('quantity');
            }),
            'totalSoldQuantity' =>  $this->when($this->needToInclude($request, 'stock.orderProductQuantity'), function () {
                return ($this->orderProducts->sum('quantity') - $this->productReturned->sum('quantity')); // calculate total sold quantity.
            }),
            'alertQuantity' => $this->alertQuantity,
            'unitCost' => $this->unitCost,
            'tax' => $this->tax,
            'stockPrice'=> round(($this->quantity * $this->unitCost),2),
            'unitPrice' => $this->unitPrice,
            'sellingPrice' => $this->unitPrice,
            'purchasePrice' => $this->unitCost,
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
            'wcStockId' => $this->wcStockId,
            'ecomPublishedAt' => $this->ecomPublishedAt,
            'permalink' => $this->permalink,
            'stockLogs' => $this->when($this->needToInclude($request, 'stock.stockLogs'), function () {
                return StockLogResource::collection($this->stockLogs);
            }),
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'stock.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
