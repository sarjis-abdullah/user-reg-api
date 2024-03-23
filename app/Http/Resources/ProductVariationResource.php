<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;

class ProductVariationResource extends Resource
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
            'id' => $this->id,
            'createdByUserId' => $this->createdByUserId,
            'productId' => $this->productId,
            'size' => $this->size,
            'color' => $this->color,
            'material' => $this->material,
            'badge' => $this->title(),
            'isDeletable' => !count($this->stocks),
        ];
    }
}
