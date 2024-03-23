<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class SubDepartmentResource extends Resource
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
            'createdByUser' => $this->when($this->needToInclude($request, 'subDepartment.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'name' => $this->name,
            'department_id' => $this->department_id,
            'department' => $this->when($this->needToInclude($request, 'subDepartment.department'), function () {
                return new DepartmentResource($this->department);
            }),
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'subDepartment.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
