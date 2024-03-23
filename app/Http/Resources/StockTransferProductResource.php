<?php

namespace App\Http\Resources;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockTransferProductResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $hasStock = $this->stock instanceof Stock;
        return [
            'id' => $this->id,
            'fromBranchId' => $this->fromBranchId,
            'fromBranch' => $this->when($this->needToInclude($request, 'stp.fromBranch'), function () {
                return new BranchResource($this->fromBranch);
            }),
            'toBranchId' => $this->toBranchId,
            'toBranch' => $this->when($this->needToInclude($request, 'stp.toBranch'), function () {
                return new BranchResource($this->toBranch);
            }),
            'productId' => $this->productId,
            'product' => $this->when($this->needToInclude($request, 'stp.product'), function () {
                return new ProductResource($this->product);
            }),
            'increaseCostPriceAmount' => $this->increaseCostPriceAmount,
            'unitCostFromBranch' => $hasStock ? $this->stock->unitCost : null,
            'unitCostToBranch' => $this->unitCostToBranch,
            'unitPrice' => $hasStock ? $this->stock->unitPrice : null,
            'sku' => $this->sku,
            'quantity' => (float) $this->quantity,
            'totalAmount' => $this->quantity * $this->unitCostToBranch,

            'fromBranchTotalAmount' => $hasStock ? ($this->quantity * optional($this->stock)->unitCost) : 0,

            'totalSellingAmount' => $hasStock ? $this->quantity * $this->stock->unitPrice : 0,
            'comment' => $this->comment,
            'date' => $this->date,
            'status' => $this->status,
            'createdByUserId' => $this->createdByUserId,
            'updatedByUserId' => $this->updatedByUserId,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }

    protected function calculatedUnitCost()
    {
        if(!is_null($this->increaseCostPriceAmount)) {
            return (float) $this->stock->unitPrice - ((float) $this->increaseCostPriceAmount * (float) $this->stock->unitPrice) / 100;
        } else {
            return $this->stock->unitCost;
        }
    }
}
