<?php

namespace App\Services\Helpers;

use App\Models\Attachment;
use App\Repositories\Contracts\AttachmentRepository;
use Illuminate\Support\Str;

class BarcodeGenerateHelper
{
    /**
     * generate barcode
     *
     * @param array $data
     */
    public static function generateBarcode(array $data)
    {
        $image = base64_decode((new \Milon\Barcode\DNS1D)->getBarcodePNG($data['barcode'], $data['barcodeType']));

        $directoryName = Attachment::getDirectoryName($data['attachmentType']);

        $data['fileName'] = Str::random(20) . '_' . $data['resourceId'] .'_' . $data['barcode'] . '.png';
        \Storage::put($directoryName . '/' . $data['fileName'], $image, 'public');

        $attachmentRepository = app(AttachmentRepository::class);

        $attachmentRepository->save([
            'onlySaveInfo' => true,
            'fileName' => $data['fileName'],
            'fileType' => 'image/png',
            'type' => $data['attachmentType'],
            'resourceId' => $data['resourceId']
        ]);
    }
}
