<?php

namespace App\Http\Resources\Reports;

use App\Http\Resources\Resource;
use Illuminate\Http\Request;

class ProductWiseProfitResource extends Resource
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
            'productName' => optional($this->product)->name,
            'productId' => optional($this->product)->id,
            'unitName' => optional(optional($this->product)->unit)->name,
            'sku' => $this->sku,
            'stockId' => $this->id,
            'totalSaleQuantity' => round($this->orderProducts->sum('quantity'), 2),
            'totalSaleAmount' => round($this->orderProducts->sum('amount'), 2),
            'totalDiscountAmount' => round($this->orderProducts->sum('discount'), 2),
            'totalTaxAmount' => round($this->orderProducts->sum('tax'), 2),
            'totalProfitAmount' => round($this->orderProducts->sum('profitAmount'), 2),
            'totalGrossProfitAmount' => round($this->orderProducts->sum('grossProfit'), 2),

            'totalReturnQuantity' => round($this->orderProductReturnByStockId->sum('quantity'), 2),
            'totalReturnAmount' => round($this->orderProductReturnByStockId->sum('returnAmount'),2),
            'totalReturnProfitAmount' =>  round($this->orderProductReturnByStockId->sum('profitAmount'),2),
            'totalReturnDiscountAmount' =>  round($this->orderProductReturnByStockId->sum('discountAmount'),2),

            'totalNetGrossProfitAmount' =>  round(($this->orderProducts->sum('grossProfit') - ($this->orderProductReturnByStockId->sum('profitAmount') - $this->orderProductReturnByStockId->sum('discountAmount'))),2),
            'totalNetAmount' =>  (round($this->orderProducts->sum('amount'), 2) - round($this->orderProductReturnByStockId->sum('returnAmount'),2)),
            'totalNetSaleQuantity' => (round($this->orderProducts->sum('quantity'), 2) - round($this->orderProductReturnByStockId->sum('quantity'), 2)),
        ];

        /*return [
            'productName' => $this->resource->productName,
            'productId' => $this->resource->productId,
            'unitName' => $this->resource->unitName,
            'sku' => $this->resource->sku,
            'stockId' => $this->resource->stockId,
            'totalSaleQuantity' => round($this->resource->totalSaleQuantity, 2),
            'totalSaleAmount' => round($this->resource->totalSaleAmount, 2),
            'totalDiscountAmount' => round($this->resource->totalDiscountAmount, 2),
            'totalTaxAmount' => round($this->resource->totalTaxAmount, 2),
            'totalProfitAmount' => round($this->resource->totalProfitAmount, 2),
            'totalGrossProfitAmount' => round($this->resource->totalGrossProfitAmount, 2),
            'totalReturnQuantity' => round($this->resource->totalReturnQuantity, 2),
            'totalReturnAmount' => round($this->resource->totalReturnAmount,2),
            'totalReturnProfitAmount' =>  round($this->resource->totalReturnProfitAmount,2),
            'totalReturnDiscountAmount' =>  round($this->resource->totalReturnDiscountAmount,2),
            'totalNetGrossProfitAmount' =>  round(($this->resource->totalGrossProfitAmount - ($this->resource->totalReturnProfitAmount - $this->resource->totalReturnDiscountAmount)),2),
            'totalNetAmount' =>  (round($this->resource->totalSaleAmount, 2) - round($this->resource->totalReturnAmount,2)),
            'totalNetSaleQuantity' => (round($this->resource->totalSaleQuantity, 2) - round($this->resource->totalReturnQuantity, 2)),
        ];*/
    }
}
