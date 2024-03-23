<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class EmployeeResource extends Resource
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
            'createdByUser' => $this->when($this->needToInclude($request, 'employee.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'userId' => $this->userId,
            'user' => $this->when($this->needToInclude($request, 'employee.user'), function () {
                return new UserResource($this->user);
            }),
            'userRoleId' => $this->userRoleId,
            'userRole' => $this->when($this->needToInclude($request, 'employee.userRole'), function () {
                return new UserRoleResource($this->userRole);
            }),
            'companyId' => $this->companyId,
            'company' => $this->when($this->needToInclude($request, 'employee.company'), function () {
                return new CompanyResource($this->company);
            }),
            'branchId' => $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'employee.branch'), function () {
                return new BranchResource($this->branch);
            }),
            'title' => $this->title,
            'level' => $this->level,
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'employee.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
