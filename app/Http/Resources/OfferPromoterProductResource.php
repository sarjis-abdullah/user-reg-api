<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferPromoterProductResource extends Resource
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
            'productName' => $this->product->name,
            'productId' => $this->product->id,
            'type' => 'promoter-product',
            //Todo : If needed
            // 'product' => $this->when($this->needToInclude($request, 'opp.p'), function () {
            //     return new ProductResource($this->product);
            //  }),
            'stocks' => StockResource::collection($this->product->stocks),
            'bundleId' => $this->bundleId,
            'quantity' => $this->quantity,
        ];
    }
}
