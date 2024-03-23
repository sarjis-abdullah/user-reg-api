<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ProductResource extends Resource
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
            'createdByUser' => $this->when($this->needToInclude($request, 'product.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'archivedByUserId' => $this->archivedByUserId,
            'archivedByUser' => $this->when($this->needToInclude($request, 'product.archivedByUser'), function () {
                return new UserResource($this->archivedByUser);
            }),
            'companyId' => $this->companyId,
            'company' => $this->when($this->needToInclude($request, 'product.company'), function () {
                return new CompanyResource($this->company);
            }),
            'categoryId' => $this->categoryId,
            'category' => $this->when($this->needToInclude($request, 'product.category'), function () {
                return new CategoryResource($this->category);
            }),
            'subCategoryId' => $this->subCategoryId,
            'subCategory' => $this->when($this->needToInclude($request, 'product.subCategory'), function () {
                return new SubCategoryResource($this->subCategory);
            }),
            'brandId' => $this->brandId,
            'brand' => $this->when($this->needToInclude($request, 'product.brand'), function () {
                return new BrandResource($this->brand);
            }),
            'isDiscountApplicable' => $this->isDiscountApplicable,
            'isDiscountExpired' => $this->isDiscountExpired(),
            'discountId' => $this->discountId,
            'discount' => $this->when($this->needToInclude($request, 'product.discount'), function () {
                return new DiscountResource($this->discount);
            }),
            'taxId' => $this->taxId,
            'tax' => $this->when($this->needToInclude($request, 'product.tax'), function () {
                return new TaxResource($this->tax);
            }),
            'departmentId' => $this->departmentId,
            'department' => $this->when($this->needToInclude($request, 'product.department'), function () {
                return new DepartmentResource($this->department);
            }),
            'subDepartmentId' => $this->subDepartmentId,
            'subDepartment' => $this->when($this->needToInclude($request, 'product.subDepartment'), function () {
                return new SubDepartmentResource($this->subDepartment);
            }),
            'name' => $this->name,
            'genericName' => $this->genericName,
            'selfNumber' => $this->selfNumber,
            'barcode' => $this->barcode,
            'unitId' => $this->unitId,
            'unit' => $this->when($this->needToInclude($request, 'product.unit'), function () {
                return new UnitResource($this->unit);
            }),
            'stocks' => $this->when($this->needToInclude($request, 'product.stocks'), function () use($request) {
                $stocks = isset($request->branchId) ? $this->stocks->where('branchId', $request->branchId) : $this->stocks;

                if(isset($request->quantity)) {
                    if($request->quantity > 0) {
                        $stocks = $stocks->where('quantity', '>', 0);
                    } else {
                        $stocks = $stocks->where('quantity', '<=', 0);
                    }
                }

                if(isset($request->havingStockAlertQuantity)) {
                    $stocks = $stocks->where('quantity', '<=', $this->alertQuantity);
                }
                //topical solution for stocks expired date filter
                if(isset($request->expiredEndDate)) {
                    $stocks = $stocks->where('expiredDate', '<=', $request->expiredEndDate);
                }

                if(isset($request->expiredStartDate)) {
                    $stocks = $stocks->where('expiredDate', '>=', $request->expiredStartDate);
                }

                return StockResource::collection($stocks);
            }),
//            'stockInfo' => $this->stockInfo(),
            'description' => $this->description,
            'status' => $this->status,
            'image' => $this->when($this->needToInclude($request, 'product.image'), function () {
                return new AttachmentResource($this->image);
            }),
            'barcodeImage' => $this->when($this->needToInclude($request, 'product.barcodeImage'), function () {
                return new AttachmentResource($this->barcodeImage);
            }),
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'product.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'alertQuantity' => $this->alertQuantity,
            'variationOrder' => $this->variationOrder,
            'variations' => $this->when($this->needToInclude($request, 'product.variations'), function () use($request) {
                return ProductVariationResource::collection($this->productVariations);
            }),
            'archiveStocks' => $this->when($this->needToInclude($request, 'product.archiveStocks'), function () use($request) {
                return StockResource::collection($this->archiveStocks);
            }),
            'totalStocks' => $this->totalStockQuantity,
            'stockInfo' => sprintf('%s quantity with %s variations', $this->totalStockQuantity, $this->variations),
            'isSerialNumberApplicable' => $this->isSerialNumberApplicable,
            'bundle' => $this->bundleId ? new BundleResource($this->bundle) : null,
            'created_at' => $this->created_at,
            'deleted_at' => $this->deleted_at,
            'updated_at' => $this->updated_at
        ];
    }
}
