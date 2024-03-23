<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use App\Http\Requests\UserRole\IndexRequest;
use App\Http\Requests\UserRole\StoreRequest;
use App\Http\Requests\UserRole\UpdateRequest;
use App\Http\Resources\UserRoleResource;
use App\Repositories\Contracts\UserRoleRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserRoleController extends Controller
{
    /**
     * @var UserRoleRepository
     */
    protected $userRoleRepository;

    /**
     * UserRoleController constructor.
     * @param UserRoleRepository $userRoleRepository
     */
    public function __construct(UserRoleRepository $userRoleRepository)
    {
        $this->userRoleRepository = $userRoleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $this->authorize('list', [UserRole::class, $request->get('userId')]);

        $userRoles = $this->userRoleRepository->findBy($request->all());

        return UserRoleResource::collection($userRoles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return UserRoleResource
     * @throws AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('store', [UserRole::class, $request->get('userId')]);

        $userRole = $this->userRoleRepository->save($request->all());

        return new UserRoleResource($userRole);
    }

    /**
     * Display the specified resource.
     *
     * @param UserRole $userRole
     * @return UserRoleResource
     * @throws AuthorizationException
     */
    public function show(UserRole $userRole)
    {
        $this->authorize('show', $userRole);

        return new UserRoleResource($userRole);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param UserRole $userRole
     * @return UserRoleResource
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, UserRole $userRole)
    {
        $this->authorize('update', $userRole);

        $userRole = $this->userRoleRepository->update($userRole, $request->all());

        return new UserRoleResource($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param UserRole $userRole
     * @return JsonResponse|void
     * @throws AuthorizationException
     */
    public function destroy(UserRole $userRole)
    {
        $this->authorize('destroy', $userRole);

        $this->userRoleRepository->delete($userRole);

        return response()->json(null, 204);
    }
}
