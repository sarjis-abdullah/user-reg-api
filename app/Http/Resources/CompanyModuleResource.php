<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyModuleResource extends JsonResource
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
            'companyId' => $this->companyId,
            'company' => $this->when($this->needToInclude($request, 'cm.company'), function () {
                return new CompanyResource($this->company);
            }),
            'moduleId' => $this->moduleId,
            'module' => $this->when($this->needToInclude($request, 'cm.module'), function () {
                return new ModuleResource($this->module);
            }),
            'isActive' => $this->isActive,
            'activationDate' => $this->activationDate,
            'updatedByUserId' => $this->updatedByUserId,
        ];
    }
}
