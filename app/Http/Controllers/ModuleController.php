<?php

namespace App\Http\Controllers;

use App\Http\Requests\Module\IndexRequest;
use App\Http\Requests\Module\StoreRequest;
use App\Http\Requests\Module\UpdateRequest;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use App\Repositories\Contracts\ModuleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ModuleController extends Controller
{
    /**
     * @var ModuleRepository
     */
    protected $moduleRepository;

    /**
     * ProductController constructor.
     * @param ModuleRepository $moduleRepository
     */
    public function __construct(ModuleRepository $moduleRepository)
    {
        $this->moduleRepository = $moduleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $modules = $this->moduleRepository->findBy($request->all());

        return ModuleResource::collection($modules);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return ModuleResource
     */
    public function store(StoreRequest $request)
    {
        $module = $this->moduleRepository->save($request->all());

        return new ModuleResource($module);
    }

    /**
     * Display the specified resource.
     *
     * @param Module $module
     * @return ModuleResource
     */
    public function show(Module $module)
    {
        return new ModuleResource($module);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Module $module
     * @return ModuleResource
     */
    public function update(UpdateRequest $request, Module $module)
    {
        $module = $this->moduleRepository->update($module, $request->all());

        return new ModuleResource($module);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Module $module
     * @return JsonResponse
     */
    public function destroy(Module $module)
    {
        $this->moduleRepository->delete($module);

        return \response()->json(null, 204);
    }
}
