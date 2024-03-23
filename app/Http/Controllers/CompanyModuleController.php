<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyModule\IndexRequest;
use App\Http\Requests\CompanyModule\StoreRequest;
use App\Http\Requests\CompanyModule\UpdateRequest;
use App\Http\Resources\CompanyModuleResource;
use App\Models\CompanyModule;
use App\Repositories\Contracts\CompanyModuleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyModuleController extends Controller
{
    /**
     * @var CompanyModuleRepository
     */
    protected $companyModuleRepository;

    /**
     * CompanyModuleController constructor.
     * @param CompanyModuleRepository $companyModuleRepository
     */
    public function __construct(CompanyModuleRepository $companyModuleRepository)
    {
        $this->companyModuleRepository = $companyModuleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $companies = $this->companyModuleRepository->findBy($request->all());

        return CompanyModuleResource::collection($companies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return CompanyModuleResource
     */
    public function store(StoreRequest $request)
    {
        $companyModule = $this->companyModuleRepository->save($request->all());

        return new CompanyModuleResource($companyModule);
    }

    /**
     * Display the specified resource.
     *
     * @param CompanyModule $companyModule
     * @return CompanyModuleResource
     */
    public function show(CompanyModule $companyModule)
    {
        return new CompanyModuleResource($companyModule);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param CompanyModule $companyModule
     * @return CompanyModuleResource
     */
    public function update(UpdateRequest $request, CompanyModule $companyModule)
    {
        $companyModule = $this->companyModuleRepository->update($companyModule, $request->all());

        return new CompanyModuleResource($companyModule);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CompanyModule $companyModule
     * @return JsonResponse
     */
    public function destroy(CompanyModule $companyModule)
    {
        $this->companyModuleRepository->delete($companyModule);

        return response()->json(null, 204);
    }
}
