<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserRoleModulePermissionResource extends Resource
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
            'createdByUserId' => $this->createdByUserId,
            'branchId' => $this->branchId,
            'userId' => $this->userId,
            'user' => $this->when($this->needToInclude($request, 'urmp.user'), function () {
                return new UserResource($this->user);
            }),
            'roleId' => $this->roleId,
            'role' => $this->when($this->needToInclude($request, 'urmp.role'), function () {
                return new RoleResource($this->role);
            }),
            'moduleActionNames' => $this->moduleActionNames,
            'moduleActionIds' => $this->moduleActionIds,
            'moduleActions' => $this->when($this->needToInclude($request, 'urmp.moduleActions'), function () {
                return ModuleActionResource::collection($this->moduleActions());
            }),
            'updatedByUserId' => $this->updatedByUserId,
        ];
    }
}
