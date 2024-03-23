<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attachment\IndexRequest;
use App\Http\Requests\Attachment\StoreRequest;
use App\Http\Resources\AttachmentResource;
use App\Repositories\Contracts\AttachmentRepository;
use App\Models\Attachment;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttachmentController extends Controller
{
    /**
     * @var AttachmentRepository
     */
    protected $attachmentRepository;

    /**
     * AttachmentController constructor.
     *
     * @param AttachmentRepository $attachmentRepository
     */
    public function __construct(AttachmentRepository $attachmentRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * show the attachments
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $this->authorize('list', Attachment::class);

        $attachments = $this->attachmentRepository->findBy($request->all());

        return AttachmentResource::collection($attachments);
    }

    /**
     * create a attachment
     *
     * @param StoreRequest $request
     * @return AttachmentResource
     */
    public function store(StoreRequest $request)
    {
        $attachment = $this->attachmentRepository->save($request->all());

        return  new AttachmentResource($attachment);
    }

    /**
     * Show a attachment
     *
     * @param Attachment $attachment
     * @return AttachmentResource
     * @throws AuthorizationException
     */
    public function show(Attachment $attachment)
    {
        $this->authorize('show', $attachment);

        return new AttachmentResource($attachment);
    }

    /**
     * delete a attachment
     *
     * @param Attachment $attachment
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Attachment $attachment)
    {
        $this->authorize('destroy',  [Attachment::class, 'attachment_delete']);

        $this->attachmentRepository->delete($attachment);

        return response()->json(null, 204);
    }
}
