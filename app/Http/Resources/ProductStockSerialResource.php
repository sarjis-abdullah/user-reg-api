<?php

namespace App\Http\Resources;

class ProductStockSerialResource extends Resource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'createdByUserId' => $this->createdByUserId,
            'productId' => $this->productId,
            'stockId' => $this->stockId,
            'productStockSerialId' => $this->productStockSerialId,
            'updatedByUserId' => $this->updatedByUserId,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'createdByUser' => $this->when($this->needToInclude($request, 'productStockSerial.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'product' => $this->when($this->needToInclude($request, 'productStockSerial.product'), function () {
                return new ProductResource($this->product);
            }),
            'stock' => $this->when($this->needToInclude($request, 'productStockSerial.stock'), function () {
                return new StockResource($this->stock);
            }),
            ];
    }
}
