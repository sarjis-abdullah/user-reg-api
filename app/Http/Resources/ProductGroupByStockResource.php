<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ProductGroupByStockResource extends Resource
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
            'id' => $this->product ? $this->product->id : null,
            'createdByUserId' => $this->createdByUserId,
            'companyId' => $this->product ? $this->product->companyId : null,
            'companyName' => $this->product && $this->product->company ? $this->product->company->name : null,
            'categoryId' => $this->product ? $this->product->categoryId : null,
            'categoryName' => $this->product && $this->product->category ? $this->product->category->name : null,
            'subCategoryId' => $this->product ? $this->product->subCategoryId : null,
            'branchId' => $this->branchId,
            'branchName' => $this->branch ? $this->branch->name : null,
            'brandId' => $this->product ? $this->product->brandId : null,
            'brandName' => $this->product && $this->product->brand ? $this->product->brand->name : null,
            'discountId' => $this->product ? $this->product->discountId : null,
            "discountType" => $this->product && $this->product->discount ? $this->product->discount->type : null,
            "discountAmount"=> $this->product && $this->product->discount ? $this->product->discount->amount : null,
            "discountEndDate"=> $this->product && $this->product->discount ?  $this->product->discount->endDate : null,
            'taxId' => $this->product ? $this->product->taxId : null,
            "taxType"=> $this->product &&$this->product->tax ? $this->product->tax->type : null,
            "taxAmount"=> $this->product && $this->product->tax ? $this->product->tax->amount : null,
            "taxAction"=> $this->product && $this->product->tax ? $this->product->tax->action : null,
            'name' => $this->product ? $this->product->name : null,
            'image' => $this->product && $this->product->image ? new AttachmentResource($this->product->image) : null,
            'genericName' => $this->product ? $this->product->genericName : null,
            'selfNumber' => $this->product ? $this->product->selfNumber : null,
            'barcode' => $this->product ? $this->product->barcode : null,
            'isDiscountApplicable' => $this->product->isDiscountApplicable,
            'unitId' => $this->product ? $this->product->unitId : null,
            'unitName' => $this->product && $this->product->unit ? $this->product->unit->name : null,
            'isFraction' => $this->product && $this->product->unit ? $this->product->unit->isFraction : null,
            'description' => $this->product ? $this->product->description : null,
            'status' => $this->product ? $this->product->status : null,
            'stockId' => $this->id,
            'sku' => $this->sku,
            "variation"=> $this->productVariation ? $this->productVariation->title() : null,
            'expiredDate' => $this->expiredDate,
            'quantity' => $this->quantity,
            'totalSaleQuantity' => $this->totalSaleQuantity ?? null,
            'alertQuantity' => $this->product ? $this->product->alertQuantity : null,
            'bundle' => $this->product && $this->product->bundle ? new BundleResource($this->product->bundle) : null,
            'unitCost' => $this->unitCost,
            'unitPrice' => $this->unitPrice,
            'created_at' => $this->created_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
