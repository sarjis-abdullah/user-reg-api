<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class DeliveryResource extends Resource
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
            'type' => $this->type,
            'deliveryAgencyId' => $this->deliveryAgencyId,
            'deliveryAgency' => $this->when($this->needToInclude($request, 'stf.deliveryAgency'), function () {
                return new DeliveryAgencyResource($this->deliveryAgency);
            }),
            'deliveryPersonName' => $this->deliveryPersonName,
            'deliveryPersonId'=>$this->deliveryPersonId,
            'trackingNumber' => $this->trackingNumber,
            'fromDeliveryPhone' => $this->fromDeliveryPhone,
            'toDeliveryPhone' => $this->toDeliveryPhone,
            'fromDeliveryAddress' => $this->fromDeliveryAddress,
            'toDeliveryAddress' => $this->toDeliveryAddress,
            'status' => $this->status,
            'note' => $this->note,
            'createdByUserId' => $this->createdByUserId,
            'updatedByUserId' => $this->updatedByUserId,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
