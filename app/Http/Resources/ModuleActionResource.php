<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ModuleActionResource extends Resource
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
            'moduleId' => $this->moduleId,
            'module' => $this->when($this->needToInclude($request, 'ma.module'), function () {
                return new ModuleResource($this->module);
            }),
            'name' => $this->name,
            'hasAccessUpToRoleId' => $this->hasAccessUpToRoleId,
            'hasAccessUpToRole' => $this->when($this->needToInclude($request, 'ma.role'), function () {
                return new RoleResource($this->role);
            }),
            'updatedByUserId' => $this->updatedByUserId,
        ];
    }
}
