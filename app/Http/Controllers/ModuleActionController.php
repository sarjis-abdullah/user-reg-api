<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModuleAction\IndexRequest;
use App\Http\Requests\ModuleAction\StoreRequest;
use App\Http\Requests\ModuleAction\UpdateRequest;
use App\Http\Resources\ModuleActionResource;
use App\Models\ModuleAction;
use App\Repositories\Contracts\ModuleActionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ModuleActionController extends Controller
{
    /**
     * @var ModuleActionRepository
     */
    protected $moduleActionRepository;

    /**
     * ProductController constructor.
     * @param ModuleActionRepository $moduleActionRepository
     */
    public function __construct(ModuleActionRepository $moduleActionRepository)
    {
        $this->moduleActionRepository = $moduleActionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $moduleActions = $this->moduleActionRepository->findBy($request->all());

        return ModuleActionResource::collection($moduleActions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return ModuleActionResource
     */
    public function store(StoreRequest $request)
    {
        $moduleAction = $this->moduleActionRepository->save($request->all());

        return new ModuleActionResource($moduleAction);
    }

    /**
     * Display the specified resource.
     *
     * @param ModuleAction $moduleAction
     * @return ModuleActionResource
     */
    public function show(ModuleAction $moduleAction)
    {
        return new ModuleActionResource($moduleAction);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param ModuleAction $moduleAction
     * @return ModuleActionResource
     */
    public function update(UpdateRequest $request, ModuleAction $moduleAction)
    {
        $moduleAction = $this->moduleActionRepository->update($moduleAction, $request->all());

        return new ModuleActionResource($moduleAction);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ModuleAction $moduleAction
     * @return JsonResponse
     */
    public function destroy(ModuleAction $moduleAction)
    {
        $this->moduleActionRepository->delete($moduleAction);

        return \response()->json(null, 204);
    }
}
