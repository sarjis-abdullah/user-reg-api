<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserRoleResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'createdByUserId' => $this->createdByUserId,
            'createdByUser' => $this->when($this->needToInclude($request, 'ur.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),

            'userId' => $this->userId,
            'user' => $this->when($this->needToInclude($request, 'ur.user'), function () {
                return new UserResource($this->user);
            }),
            'roleId' => $this->roleId,
            'role' => $this->when($this->needToInclude($request, 'ur.role'), function () {
                return new RoleResource($this->role);
            }),
            'permissions' => $this->when($this->needToInclude($request, 'ur.permissions'), function () {
                return new UserRoleModulePermissionResource($this->permissions);
            }),
            'branchId' => $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'ur.branch'), function () {
                return new BranchResource($this->branch);
            })
        ];
    }
}
