<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRoleModulePermission\IndexRequest;
use App\Http\Requests\UserRoleModulePermission\StoreRequest;
use App\Http\Requests\UserRoleModulePermission\UpdateRequest;
use App\Http\Resources\UserRoleModulePermissionResource;
use App\Models\UserRoleModulePermission;
use App\Repositories\Contracts\UserRoleModulePermissionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserRoleModulePermissionController extends Controller
{
    /**
     * @var UserRoleModulePermissionRepository
     */
    protected $userRoleModulePermissionRepository;

    /**
     * UserRoleModulePermissionController constructor.
     * @param UserRoleModulePermissionRepository $userRoleModulePermissionRepository
     */
    public function __construct(UserRoleModulePermissionRepository $userRoleModulePermissionRepository)
    {
        $this->userRoleModulePermissionRepository = $userRoleModulePermissionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $userRoleModulePermissions = $this->userRoleModulePermissionRepository->findBy($request->all());

        return UserRoleModulePermissionResource::collection($userRoleModulePermissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return UserRoleModulePermissionResource
     */
    public function store(StoreRequest $request)
    {
        $userRoleModulePermission = $this->userRoleModulePermissionRepository->save($request->all());

        return new UserRoleModulePermissionResource($userRoleModulePermission);
    }

    /**
     * Display the specified resource.
     *
     * @param UserRoleModulePermission $userRoleModulePermission
     * @return UserRoleModulePermissionResource
     */
    public function show(UserRoleModulePermission $userRoleModulePermission)
    {
        return new UserRoleModulePermissionResource($userRoleModulePermission);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param UserRoleModulePermission $userRoleModulePermission
     * @return UserRoleModulePermissionResource
     */
    public function update(UpdateRequest $request, UserRoleModulePermission $userRoleModulePermission)
    {
        $userRoleModulePermission = $this->userRoleModulePermissionRepository->update($userRoleModulePermission, $request->all());

        return new UserRoleModulePermissionResource($userRoleModulePermission);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param UserRoleModulePermission $userRoleModulePermission
     * @return JsonResponse
     */
    public function destroy(UserRoleModulePermission $userRoleModulePermission)
    {
        $this->userRoleModulePermissionRepository->delete($userRoleModulePermission);

        return response()->json(null, 204);
    }
}
