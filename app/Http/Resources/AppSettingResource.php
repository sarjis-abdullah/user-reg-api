<?php

namespace App\Http\Resources;

use App\Models\Attachment;
use App\Repositories\Contracts\AttachmentRepository;
use Illuminate\Http\Request;

class AppSettingResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // look if there are any attachment
        $attachmentRepository = app(AttachmentRepository::class);
        $settings = $this->settings;
        foreach ($this->settings as $key => $value) {
            if (str_contains(strtolower($key), 'attachmentid')) {
                $attachment = $attachmentRepository->findOne($value);
                $newField = str_ireplace('id', '', $key );
                if ($attachment instanceof Attachment) {
                    $settings->{$newField} = new AttachmentResource($attachment);
                } else {
                    $settings->{$newField} = null;
                }

            }
        }

        return [
            'id' => $this->id,
            'branchId' => $this->branchId,
            'branch' => new BranchResource($this->branch),
            'type' => $this->type,
            'settings' => $settings
        ];
    }
}
