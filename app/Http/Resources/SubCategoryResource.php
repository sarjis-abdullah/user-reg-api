<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class SubCategoryResource extends Resource
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
            'createdByUser' => $this->when($this->needToInclude($request, 'sc.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'categoryId' => $this->categoryId,
            'category' => $this->when($this->needToInclude($request, 'sc.category'), function () {
                return new CategoryResource($this->category);
            }),
            'name' => $this->name,
            'code' => $this->code,
            'wcSubCategoryId' => $this->wcSubCategoryId,
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'sc.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
