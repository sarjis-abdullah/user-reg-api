<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Http\Requests\Role\IndexRequest;
use App\Http\Requests\Role\StoreRequest;
use App\Http\Requests\Role\UpdateRequest;
use App\Http\Resources\RoleResource;
use App\Repositories\Contracts\RoleRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoleController extends Controller
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * RoleController constructor.
     * @param RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
//        $this->authorize('list', [Role::class]);

        $roles = $this->roleRepository->findBy($request->all());

        return RoleResource::collection($roles);
    }

    /**
     * create a Role
     *
     * @param StoreRequest $request
     * @return RoleResource
     */
    public function store(StoreRequest $request): RoleResource
    {
//        $this->authorize('store', [Role::class]);

        $role = $this->roleRepository->save($request->all());

        return new RoleResource($role);
    }

    /**
     * Display the specified Role resource.
     *
     * @param Role $role
     * @return RoleResource
     */
    public function show(Role $role): RoleResource
    {
//        $this->authorize('show', $role);

        return new RoleResource($role);
    }

    /**
     * Update the specified Role resource in storage.
     *
     * @param UpdateRequest $request
     * @param Role $role
     * @return RoleResource
     */
    public function update(UpdateRequest $request, Role $role): RoleResource
    {
//        $this->authorize('update', $role);

        $role = $this->roleRepository->update($role, $request->all());

        return new RoleResource($role);
    }

    /**
     * Remove the specified Role resource from storage.
     *
     * @param Role $role
     * @return JsonResponse
     */
    public function destroy(Role $role): JsonResponse
    {
//        $this->authorize('destroy', $role);

        $this->roleRepository->delete($role);

        return response()->json(null, 204);
    }
}
