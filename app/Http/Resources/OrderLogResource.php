<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;

class OrderLogResource extends Resource
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
            'orderId' => $this->orderId,
            'order' => $this->when($this->needToInclude($request, 'ol.order'), function () {
                return new OrderResource($this->order);
            }),
            'comment' => $this->comment,
            'status' => $this->status,
            'paymentStatus' => $this->paymentStatus,
            'deliveryStatus' => $this->deliveryStatus,
            'updatedByUserId' => $this->updatedByUserId,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
