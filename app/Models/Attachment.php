<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CommonModelFeatures;

class Attachment extends Model
{
    use CommonModelFeatures;

    const ATTACHMENT_TYPE_GENERIC = 'generic';
    const ATTACHMENT_TYPE_USER_PROFILE = 'user-profile';
    const ATTACHMENT_TYPE_SUPPLIER= 'supplier';
    const ATTACHMENT_TYPE_PRODUCT= 'product';
    const ATTACHMENT_TYPE_CATEGORY = 'category';
    const ATTACHMENT_TYPE_BRAND = 'brand';
    const ATTACHMENT_TYPE_PRODUCT_BARCODE = 'product-barcode';
    const ATTACHMENT_TYPE_INVOICE = 'invoice';
    const ATTACHMENT_TYPE_REFERENCE = 'reference';

    const ATTACHMENT_VARIATION_DEFAULT = 'default';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'type',
        'resourceId',
        'fileName',
        'variation',
        'descriptions',
        'fileType',
        'fileSize',
        'hasAvatarSize',
        'hasThumbnailSize',
        'hasMediumSize',
        'hasLargeSize',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'hasAvatarSize' => 'boolean',
        'hasThumbnailSize' => 'boolean',
        'hasMediumSize' => 'boolean',
        'hasLargeSize' => 'boolean',
    ];


    /**
     * Get storage directory name by attachment type
     *
     * @param $attachmentType
     * @return string
     */
    public static function getDirectoryName($attachmentType)
    {
        $directoryName = '';
        switch ($attachmentType) {
            case self::ATTACHMENT_TYPE_GENERIC:
                $directoryName = 'generic';
                break;
            case self::ATTACHMENT_TYPE_USER_PROFILE:
                $directoryName = 'userProfiles';
                break;
            case self::ATTACHMENT_TYPE_CATEGORY:
                $directoryName = 'categories';
                break;
            case self::ATTACHMENT_TYPE_BRAND:
                $directoryName = 'brands';
                break;
            case self::ATTACHMENT_TYPE_SUPPLIER:
                $directoryName = 'suppliers';
                break;
            case self::ATTACHMENT_TYPE_PRODUCT_BARCODE:
                $directoryName = 'productBarcodes';
                break;
            case self::ATTACHMENT_TYPE_PRODUCT:
                $directoryName = 'products';
                break;
            case self::ATTACHMENT_TYPE_INVOICE:
                $directoryName = 'invoices';
                break;
            case self::ATTACHMENT_TYPE_REFERENCE:
                $directoryName = 'references';
                break;
        }

        return $directoryName;
    }


    /**
     * get access type of the attachment
     *
     * @param string $attachmentType
     * @return string
     */
    public function getAccessTypeByAttachmentType(string $attachmentType)
    {
        $accessType = 'public';

        if ($attachmentType == self::ATTACHMENT_TYPE_USER_PROFILE) {
            $accessType = 'private';
        }

        return $accessType;
    }

    /**
     * get image width and height by image type title
     *
     * @param $title
     * @return array
     */
    public function getImageSizeByTypeTitle($title)
    {
        $sizes = ['width' => 150, 'height' => 150];
        switch (strtolower($title)) {
            case 'avatar':
                $sizes = ['width' => 40, 'height' => 40];
                break;
            case 'thumbnail':
                $sizes = ['width' => 150, 'height' => 150];
                break;
            case 'medium':
                $sizes = ['width' => 300, 'height' => 300];
                break;
            case 'large':
                $sizes = ['width' => 1024, 'height' => 1024];
                break;

        }

        return $sizes;
    }

    /**
     * get attachment file path by type-title(thumbnail, medium, large etc.)
     *
     * @param string $typeTitle
     * @return string
     */
    public function getAttachmentDirectoryPathByTypeTitle($typeTitle = '')
    {
        switch (strtolower($typeTitle)) {
            case 'thumbnail':
            case 'large':
            case 'medium':
            case 'avatar':
                $path = $typeTitle . '/' . $this->fileName;
                break;
            default:
                $path = $this->fileName;

        }

        $directoryName = $this->getDirectoryName($this->type);

        return $directoryName . '/' . $path;
    }

    /**
     * see if image type is available for that attachment
     *
     * @param string $imageType
     * @return mixed
     */
    public function isImageSizeAvailable($imageType = '')
    {
        switch (strtolower($imageType)) {
            case '': //for normal file url
                return true;
            case 'avatar':
                return $this->hasAvatarSize;
            case 'thumbnail':
                return $this->hasThumbnailSize;
            case 'medium':
                return $this->hasMediumSize;
            case 'large':
                return $this->hasLargeSize;
            default:
                return false;
        }
    }

    /**
     * generate file URL by type
     *
     * @param string $imageType
     */
    public function getFileUrl($imageType = '')
    {
        $accessType = $this->getAccessTypeByAttachmentType($this->type);

        if ($accessType == 'private') {
            return $this->isImageSizeAvailable($imageType) ? \Storage::temporaryUrl($this->getAttachmentDirectoryPathByTypeTitle($imageType), Carbon::now()->addMinutes(10)) : null;
        } else {
            return $this->isImageSizeAvailable($imageType) ? \Storage::url($this->getAttachmentDirectoryPathByTypeTitle($imageType)) : null;
        }
    }
}
