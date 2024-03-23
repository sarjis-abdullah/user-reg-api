<?php


namespace App\Repositories\Contracts;


use App\Models\Attachment;

interface AttachmentRepository extends BaseRepository
{
    /**
     * get all attachment types
     *
     * @return array
     */
    public function getAllAttachmentTypes(): array;

    /**
     * update resourceId if resourceId has not been created earlier
     *
     * @param Attachment $attachment
     * @param int $id
     * @return \ArrayAccess
     */
    public function updateResourceId(Attachment $attachment, $id);

    /**
     * update all resource ids
     *
     * @param array $attachmentIds
     * @param $id
     */
    public function updateResourceIds(array $attachmentIds, $id);

    /**
     * get attachment by resource-type and resource Id
     *
     * @param string $type
     * @param int $resourceId
     * @return mixed
     */
    public function getAttachmentByTypeAndResourceId($type, $resourceId);

    /**
     * get profile pic by resource Id
     *
     * @param int $resourceId
     * @param string $size
     * @return mixed
     */
    public function getProfilePicByResourceId($resourceId, $size = 'medium');

    /**
     * copy an old attachment as a new
     *
     * @param Attachment $attachment
     * @param array $data
     * @return @return \ArrayAccess
     */
    public function copyOldAttachment(Attachment $attachment, array $data = []);
}
