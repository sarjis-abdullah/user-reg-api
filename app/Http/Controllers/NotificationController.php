<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notification\IndexRequest;
use App\Http\Requests\Notification\UpdateRequest;
use App\Http\Resources\NotificationResourceCollection;
use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepository;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * @var NotificationRepository
     */
    protected $notificationRepository;

    /**
     * @param  NotificationRepository  $notificationRepository
     */
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @return NotificationResourceCollection
     */
    public function index(IndexRequest $request): NotificationResourceCollection
    {
        $notifications = $this->notificationRepository->findBy($request->all());

        return new NotificationResourceCollection($notifications);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Notification $notification): JsonResponse
    {
        $this->notificationRepository->markReadStatus($notification, $request->get('read'));

        return response()->json(['data' => ['message' => 'ReadStatus set successfully.']], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @return JsonResponse
     */
    public function updateAll(UpdateRequest $request): JsonResponse
    {
        $this->notificationRepository->markAllReadStatus($request->get('read'));

        return response()->json(['data' => ['message' => 'ReadStatus set successfully.']], 200);
    }
}
