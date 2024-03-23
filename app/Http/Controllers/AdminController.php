<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Requests\Admin\IndexRequest;
use App\Http\Requests\Admin\StoreRequest;
use App\Http\Requests\Admin\UpdateRequest;
use App\Http\Resources\AdminResource;
use App\Repositories\Contracts\AdminRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminController extends Controller
{
    /**
     * @var AdminRepository
     */
    protected $adminRepository;

    /**
     * AdminController constructor.
     * @param AdminRepository $adminRepository
     */
    public function __construct(AdminRepository $adminRepository) {
        $this->adminRepository = $adminRepository;
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
        $this->authorize('list', Admin::class);

        $admins = $this->adminRepository->findBy($request->all());

        return AdminResource::collection($admins);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return AdminResource
     * @throws AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('store', [Admin::class, $request->get('level', Admin::LEVEL_STANDARD)]);

        $admin = $this->adminRepository->save($request->all());

        return new AdminResource($admin);
    }

    /**
     * Display the specified resource.
     *
     * @param Admin $admin
     * @return AdminResource
     * @throws AuthorizationException
     */
    public function show(Admin $admin)
    {
        $this->authorize('show', $admin);

        return new AdminResource($admin);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Admin $admin
     * @return AdminResource
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, Admin $admin)
    {
        $this->authorize('update', $admin);

        $admin = $this->adminRepository->update($admin,$request->all());

        return new AdminResource($admin);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Admin $admin
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Admin $admin)
    {
        $this->authorize('destroy', $admin);

        $this->adminRepository->delete($admin);

        return response()->json(null, 204);
    }
}
