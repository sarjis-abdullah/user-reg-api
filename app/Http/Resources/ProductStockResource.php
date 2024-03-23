<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ProductStockResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        if (request()->filled('branchId')){
            $stocks = $this->stocks
                ->where('quantity', '>', 0)
                ->where('branchId', request()->get('branchId'));
        }else{
            $stocks = $this->stocks->where('quantity', '>', 0);
        }

        return [
            'id' => $this->id,
            'createdByUserId' => $this->createdByUserId,
            'companyId' => $this->companyId,
            'categoryId' => $this->categoryId,
            'subCategoryId' => $this->subCategoryId,
            'brandId' => $this->brandId,
            'unitId' => $this->unitId,
            'discountId' => $this->discountId,
            'isDiscountApplicable' => $this->isDiscountApplicable,
            'taxId' => $this->taxId,
            'name' => $this->name,
            'genericName' => $this->genericName,
            'selfNumber' => $this->selfNumber,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'status' => $this->status,
            'alertQuantity' => $this->alertQuantity,
            'variationOrder' => $this->variationOrder,
            'updatedByUserId' => $this->updatedByUserId,
            'archivedByUserId' => $this->archivedByUserId,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'isSerialNumberApplicable' => $this->isSerialNumberApplicable,
            'totalStockQuantity' => $this->totalStockQuantity,
            'totalStockValue' => $this->totalStockValue,
            'totalSalePrice' => $this->totalSalePrice,

            'totalPurchasePrice' => $this->totalPurchasePrice,
            'category' => $this->category,
            'company' => $this->company,
            'sub_category' => $this->sub_category,
            'brand' => $this->brand,
            'image' => $this->image,
            'created_by_user' => $this->created_by_user,
            'updated_by_user' => $this->updated_by_user,
            'stocks' => StockReportResource::collection($stocks),
        ];


        /*
         * This code will be deprecated soon.
         */
        /*return [
            'id' => $this->id,
            'createdByUserId' => $this->createdByUserId,
            'createdByUser' => $this->createdByUser,
            'archivedByUserId' => $this->archivedByUserId,
            'companyId' => $this->companyId,
            'company' => $this->company,
            'categoryId' => $this->categoryId,
            'category' => $this->category,
            'subCategoryId' => $this->subCategoryId,
            'subCategory' => $this->subCategory,
            'brandId' => $this->brandId,
            'brand' => $this->brand,
            'isDiscountApplicable' => $this->isDiscountApplicable,
            'discountId' => $this->discountId,
            'discount' => $this->discount,
            'taxId' => $this->taxId,
            'tax' => $this->tax,
            'name' => $this->name,
            'genericName' => $this->genericName,
            'selfNumber' => $this->selfNumber,
            'barcode' => $this->barcode,
            'unitId' => $this->unitId,
            'unit' => $this->unit,
            'stocks' => $this->stocks,
            'description' => $this->description,
            'status' => $this->status,
            'image' => $this->image,
            'barcodeImage' => $this->barcodeImage,
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->updatedByUser,
            'alertQuantity' => $this->alertQuantity,
            'variationOrder' => $this->variationOrder,
            'totalStocks' => $this->totalStockQuantity,
            'stockInfo' => sprintf('%s quantity with %s variations', $this->totalStockQuantity, $this->variations),
            'isSerialNumberApplicable' => $this->isSerialNumberApplicable,
            'created_at' => $this->created_at,
            'deleted_at' => $this->deleted_at,
            'updated_at' => $this->updated_at
        ];*/
    }
}
