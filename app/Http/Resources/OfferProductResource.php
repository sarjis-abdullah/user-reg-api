<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferProductResource extends Resource
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
            'type' => 'offer-product',
            //Todo : If needed
            // 'product' => $this->when($this->needToInclude($request, 'opp.p'), function () {
            //     return new ProductResource($this->product);
            //  }),
            'stocks' => StockResource::collection($this->product->stocks),
            'bundleId' => $this->bundleId,
            'discountAmount' => $this->discountAmount,
            'discountType' => $this->discountType,
            'quantity' => $this->quantity,
        ];
    }
}
