<?php

namespace App\Http\Resources\Reports;

use App\Http\Resources\Resource;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierWiseStockResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
        'supplierId' => $this->resource->supplierId,
        'supplierName' => $this->resource->supplierName,
        'totalPurchaseCost' => round($this->resource->totalPurchaseCost,2),
        'totalPurchaseQuantity' => round($this->resource->totalPurchaseQuantity,2),
        'totalPurchaseReturnAmount' => round($this->resource->totalPurchaseReturnAmount,2),
        'totalPurchaseReturnSellingPrice' => round($this->resource->totalPurchaseReturnSellingPrice,2),
        'totalPurchaseReturnQuantity' => round($this->resource->totalPurchaseReturnQuantity,2),
        'totalSaleReturnAmount' => round($this->resource->totalSaleReturnAmount,2),
        'totalSaleReturnQuantity' => round($this->resource->totalSaleReturnQuantity,2),
        'totalSellingStockPurchaseCost' => round($this->resource->totalSellingStockPurchaseCost,2),
        'totalSellingStockPrice' => round($this->resource->totalSellingStockPrice,2),
        'totalSoldQuantity' => round($this->resource->totalSoldQuantity,2),
        'totalLeftStockSellingValue' => round($this->resource->totalLeftStockSellingValue,2),
        'totalLeftStockPurchaseCost' => round($this->resource->totalLeftStockPurchaseCost,2),
        'totalStockLeft' => round($this->resource->totalStockLeft,2),
        ];
    }
}
