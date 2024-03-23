<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferPromoterResource extends Resource
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
            'productName' => $this->product->name,
            'productId' => $this->product->id,
//            'product' => $this->when($this->needToInclude($request, 'opp.p'), function () {
//                return new ProductResource($this->product);
//            }),
            'availableProductQuantity' => count($this->product->stocks) ? $this->product->stocks->sum('quantity') : 0,
            'stocks' => StockResource::collection($this->product->stocks),
            'bundleId' => $this->bundleId,
            'quantity' => $this->quantity,
        ];
    }
}
