<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\Request;

class BatchUploadRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            'fileSource'   => 'required|file|max:5120|mimes:csv,txt'
        ];
    }

    public function messages()
    {
        return [
            'upload.max' => "Maximum file size to upload is 5MB. If you are uploading a photo, try to reduce its resolution to make it under 5 MB"
        ];
    }
}
