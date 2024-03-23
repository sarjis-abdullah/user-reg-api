<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;

class ModuleResource extends Resource
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
            'name' => $this->name,
            'isActive' => $this->isActive,
            'moduleActions' => $this->when($this->needToInclude($request, 'module.moduleActions'), function () {
                return ModuleActionResource::collection($this->moduleActions);
            }),
            'updatedByUserId' => $this->updatedByUserId,
        ];
    }
}
