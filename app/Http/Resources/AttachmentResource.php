<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class AttachmentResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'fileName'     => $this->fileName,
            'descriptions' => $this->descriptions,
            'type'         => $this->type,
            'createdByUserId' => $this->tycreatedByUserIdpe,
            'createdByUser' => $this->when($this->needToInclude($request, 'attachment.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'resourceId'   => $this->resourceId,
            'fileType'     => $this->fileType,
            'fileSize'     => $this->fileSize,
            'variation'      => $this->variation,
            'src'      => $this->getFileUrl(),
            'avatar' => $this->when($this->needToInclude($request, 'image.avatar'), function () {
                return $this->getFileUrl('avatar');
            }),
            'thumbnail' => $this->when($this->needToInclude($request, 'image.thumbnail'), function () {
                return $this->getFileUrl('thumbnail');
            }),
            'medium' => $this->when($this->needToInclude($request, 'image.medium'), function () {
                return $this->getFileUrl('medium');
            }),
            'large' => $this->when($this->needToInclude($request, 'image.large'), function () {
                return $this->getFileUrl('large');
            }),
        ];
    }
}
