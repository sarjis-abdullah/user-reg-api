<?php

namespace App\Http\Controllers;

use App\Http\Requests\Manager\IndexRequest;
use App\Http\Requests\Manager\StoreRequest;
use App\Http\Requests\Manager\UpdateRequest;
use App\Http\Resources\ManagerResource;
use App\Models\Manager;
use App\Repositories\Contracts\ManagerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ManagerController extends Controller
{
    /**
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     * ManagerController constructor.
     * @param ManagerRepository $managerRepository
     */
    public function __construct(ManagerRepository $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $managers = $this->managerRepository->findBy($request->all());

        return ManagerResource::collection($managers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return ManagerResource
     */
    public function store(StoreRequest $request)
    {
        $manager = $this->managerRepository->save($request->all());

        return new ManagerResource($manager);
    }

    /**
     * Display the specified resource.
     *
     * @param Manager $manager
     * @return ManagerResource
     */
    public function show(Manager $manager)
    {
        return new ManagerResource($manager);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Manager $manager
     * @return ManagerResource
     */
    public function update(UpdateRequest $request, Manager $manager)
    {
        $manager = $this->managerRepository->update($manager, $request->all());

        return new ManagerResource($manager);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Manager $manager
     * @return JsonResponse
     */
    public function destroy(Manager $manager)
    {
        $this->managerRepository->delete($manager);

        return \response()->json(null, 204);
    }
}
