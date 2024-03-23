<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UnitResource extends Resource
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
            'name' => $this->name,
            'parentId' => $this->parentId,
            'isFraction' => $this->isFraction,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
