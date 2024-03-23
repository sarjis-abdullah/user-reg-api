<?php

namespace App\Http\Requests\Attachment;

use App\Models\Attachment;
use App\Http\Requests\Request;
use App\Rules\CSVString;

class StoreRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            'type'         => 'required|in:' . implode(',', Attachment::getConstantsByPrefix('ATTACHMENT_TYPE_')),
            'fileSource'   => 'required|file|max:15360|mimes:jpeg,jpg,png,bmp,pdf,gif,mp2',
            'resourceId'   => '',
            'fileName'     => '',
            'descriptions' => '',
            'fileType'     => '',
            'fileSize'     => '',
            'variation'     => '',

            'resizeImage'  => 'boolean',
            'width'  => 'required_with:resizeImage',
            'height'  => 'required_with:resizeImage',

            'multipleTypes'  => [new CSVString(['thumbnail', 'medium', 'large', 'avatar'])],
        ];
    }

    public function messages()
    {
        return [
            'upload.max' => "Maximum file size to upload is 15MB. If you are uploading a photo, try to reduce its resolution to make it under 15 MB"
        ];
    }
}
