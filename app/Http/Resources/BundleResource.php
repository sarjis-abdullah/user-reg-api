<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundleResource extends JsonResource
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
            'customerBuys' => $this->customerBuys,
            'customerGets' => $this->customerGets,
            'offerCombinesWith' => $this->offerCombinesWith,
            'eligibleCustomerType' => $this->eligibleCustomerType,
            'usesPerOrderLimit' => $this->usesPerOrderLimit,
            'usesPerUserLimit' => $this->usesPerUserLimit,
            'usageLimit' => $this->usageLimit,
            'offerStartsAt' => $this->offerStartsAt,
            'offerEndsAt' => $this->offerEndsAt,
            'offerPromoterProducts' => count($this->offerPromoterProducts) ? OfferPromoterProductResource::collection($this->offerPromoterProducts) : [],
            'offerProducts' => count($this->offerProducts) ? OfferProductResource::collection($this->offerProducts) : [],
        ];
    }
}
