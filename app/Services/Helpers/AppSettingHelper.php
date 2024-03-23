<?php

namespace App\Services\Helpers;

use App\Repositories\Contracts\AppSettingRepository;
use App\Repositories\Contracts\AttachmentRepository;

class AppSettingHelper
{
    /**
     * @return mixed
     */
    public static function appSetting()
    {
        $appSettingRepo = app(AppSettingRepository::class);
        return $appSettingRepo->findOneBy(['branchId' => null]);
    }

    /**
     * @return void
     */
    public static function attachment()
    {
        if (isset(self::appSetting()->settings->logoAttachmentId)){
            $attachmentId = self::appSetting()->settings->logoAttachmentId;
            $attachmentRepository = app(AttachmentRepository::class);
            return $attachmentRepository->findOne($attachmentId);
        }
    }

    /**
     * @return mixed
     */
    public static function logoUrl()
    {
        return self::attachment() ? self::attachment()->getFileUrl() : public_path('/logo/dark-logo.png');
    }
}
