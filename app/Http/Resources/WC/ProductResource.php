<?php

namespace App\Http\Resources\WC;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     */
    public function toArray($request)
    {
        return  [
            ...$this->resource->getAttributes(),
            'company' => $this->company,
            'category' => $this->category,
            'subCategory' => $this->subCategory,
            'unit' => $this->unit,
            'tax' => $this->tax,
            'discount' => $this->discount,
            'brand' => $this->brand,
            'barcodeImage' => $this->barcodeImage,
            'image' => $this->image,
            'createdByUser' => $this->createdByUser,
            'updatedByUser' => $this->updatedByUser,
            'stocks' => $this->stocksForWc,
            'productVariations' => $this->productVariations,
        ];
    }
}
