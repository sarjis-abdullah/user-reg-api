<?php

namespace App\Http\Resources\Reports;

use App\Http\Resources\Resource;
use Illuminate\Http\Request;
class CategoryWiseSaleResource extends Resource
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
            'categoryId' => $this->resource->categoryId,
            'categoryName' => $this->resource->categoryName,
            'quantity' => round($this->resource->orderQuantity, 2),
            'netSaleQuantity' => round(($this->resource->orderQuantity - $this->resource->returnQuantity), 2),
            'returnQuantity' => round($this->resource->returnQuantity, 2),
            'soldAmount' => round($this->resource->soldAmount, 2),
            'returnAmount' => round($this->resource->returnAmount, 2),
            'netTotalAmount' => round(($this->resource->soldAmount - $this->resource->returnAmount), 2),
            'profitAmount' => round(($this->resource->saleProfitAmount - $this->resource->returnProfitAmount), 2),
            'grossProfitAmount' => round(($this->resource->saleGrossProfitAmount - ($this->resource->returnProfitAmount - $this->resource->returnDiscountAmount)), 2),
            'saleProfitAmount' => round($this->resource->saleProfitAmount, 2),
            'returnProfitAmount' => round($this->resource->returnProfitAmount, 2),
        ];
    }
}
