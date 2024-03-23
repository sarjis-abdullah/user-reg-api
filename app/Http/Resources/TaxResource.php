<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class TaxResource extends Resource
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
            "id" => $this->id,
            "createdByUserId" => $this->createdByUserId,
            'createdByUser' => $this->when($this->needToInclude($request, 'tax.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            "title" => $this->title,
            "amount" => $this->amount,
            "type" => $this->type,
            "action" => $this->action,
            "notes" => $this->notes,
            "wcTaxId" => $this->wcTaxId,
            "updatedByUserId" => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'tax.createdByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
        ];
    }
}
