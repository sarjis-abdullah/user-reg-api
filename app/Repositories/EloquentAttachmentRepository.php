<?php

namespace App\Repositories;

use App\Models\Attachment;
use App\Events\Attachment\AttachmentCreatedEvent;
use App\Http\Resources\AttachmentResource;
use App\Repositories\Contracts\AttachmentRepository;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

class EloquentAttachmentRepository extends EloquentBaseRepository implements AttachmentRepository
{
    /*
     * @inheritdoc
     */
    public function save(array $data): \ArrayAccess
    {
        if(isset($data['onlySaveInfo'])) {
            return parent::save($data);
        } else {
            if ($data['fileSource'] instanceof UploadedFile) {

                $filePath = $data['fileSource']->getPathname();

                if (strpos($data['fileSource']->getMimeType(), 'image') !== false) {

                    //resize image
                    if (!empty($data['resizeImage'])) {
                        $resizedImagePath = '/tmp/' . Str::random(10);
                        \Image::make($data['fileSource']->getPathname())->resize($data['width'], $data['height'])->save($resizedImagePath);
                        $filePath = $resizedImagePath;
                    }

                    //optimize image
//                ImageOptimizer::optimize($filePath);
                }
                $image = file_get_contents($filePath);
            }
            if (!isset($data['resourceId'])) {
                $data['resourceId'] = '';
            }

            $directoryName = $this->model->getDirectoryName($data['type']);
            $data['fileName'] = Str::random(20) . '_' . $data['resourceId'] . '_' . $data['fileSource']->getClientOriginalName();
            \Storage::put($directoryName . '/' . $data['fileName'], $image, 'public');
            $data['fileType'] = $data['fileSource']->getMimeType();
            $attachment = parent::save($data);

            event(new AttachmentCreatedEvent($attachment, $this->generateEventOptionsForModel(['multipleTypes' => $data['multipleTypes'] ?? []], false)));

            return $attachment;
        }
    }

    /* todo no need
     * @inheritdoc
     */
    /*public function update(\ArrayAccess $model, array $data): \ArrayAccess
    {
        $image = file_get_contents($data['fileSource']);
        $directoryName = $this->model->getDirectoryName($data['type']);
        $data['fileName'] = Str::random(20) . '_' . $data['resourceId'] . '_' . $data['fileSource']->getClientOriginalName();
        \Storage::delete($directoryName . '/' . $model->fileName);
        \Storage::put($directoryName . '/' . $data['fileName'], $image, 'public');
        return parent::update($model, $data);
    }*/

    /**
     * @inheritdoc
     */
    public function delete(\ArrayAccess $model): bool
    {
        $directoryName = $this->model->getDirectoryName($model->type);
        \Storage::delete($directoryName . '/' . $model->fileName);
        return parent::delete($model);
    }

    /**
     * @inheritdoc
     */
    public function getAllAttachmentTypes(): array
    {
        $reflectionClass = new \ReflectionClass($this->model);

        return array_filter($reflectionClass->getConstants(), function ($constant) {
            return strpos($constant, 'ATTACHMENT_TYPE_') === 0;
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @inheritDoc
     */
    public function updateResourceId(Attachment $attachment, $id)
    {
        // check if the resourceId is already assigned
        // todo move it to rules
        if (!empty($attachment->resourceId)) {
            /*throw ValidationException::withMessages([
                'resourceId' => ['Resource Id is already assigned.']
            ]);*/
            return;
        }

        return parent::update($attachment, ['resourceId' => $id]);
    }

    /**
     * @inheritDoc
     */
    public function updateResourceIds(array $attachmentIds, $id)
    {
        foreach ($attachmentIds as $attachment) {
            $attachment = $this->findOne($attachment);
            if ($attachment instanceof Attachment) {
                $this->updateResourceId($attachment, $id);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getAttachmentByTypeAndResourceId($type, $resourceId)
    {
        $profileAttachment = $this->findOneBy(['resourceId' => $resourceId, 'type' => $type]);

        return $profileAttachment;
    }

    /**
     * @inheritDoc
     */
    public function getProfilePicByResourceId($resourceId, $size = 'medium')
    {
        $profileAttachment = $this->getAttachmentByTypeAndResourceId(Attachment::ATTACHMENT_TYPE_USER_PROFILE, $resourceId);
        if ($profileAttachment instanceof Attachment) {
            return new AttachmentResource($profileAttachment);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function copyOldAttachment(Attachment $attachment, array $data = [])
    {
        $newData = array_merge($attachment->toArray(), $data);
        unset($newData['id']);

        return parent::save($newData);
    }
}
