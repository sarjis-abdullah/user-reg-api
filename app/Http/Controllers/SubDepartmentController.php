<?php

namespace App\Http\Controllers;


use App\Http\Requests\SubDepartment\IndexRequest;
use App\Http\Requests\SubDepartment\StoreRequest;
use App\Http\Requests\SubDepartment\UpdateRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\SubDepartmentResource;
use App\Models\SubDepartment;
use App\Repositories\Contracts\SubDepartmentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubDepartmentController extends Controller
{
    protected $subDepartmentRepository;

    /**
     * @param SubDepartmentRepository $subDepartmentRepository
     */
    public function __construct(SubDepartmentRepository $subDepartmentRepository)
    {
        $this->subDepartmentRepository = $subDepartmentRepository;
    }


    /**
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $subDepartments = $this->subDepartmentRepository->findBy($request->all());

        return SubDepartmentResource::collection($subDepartments);
    }


    /**
     * @param StoreRequest $request
     * @return SubDepartmentResource
     */
    public function store(StoreRequest $request): SubDepartmentResource
    {
        $department = $this->subDepartmentRepository->save($request->all());

        return new SubDepartmentResource($department);
    }


    /**
     * @param SubDepartment $subDepartment
     * @return SubDepartmentResource
     */
    public function show(SubDepartment $subDepartment): SubDepartmentResource
    {
        return new SubDepartmentResource($subDepartment);
    }

    /**
     * @param UpdateRequest $request
     * @param SubDepartment $subDepartment
     * @return DepartmentResource
     */
    public function update(UpdateRequest $request, SubDepartment $subDepartment): DepartmentResource
    {
        $department = $this->subDepartmentRepository->update($subDepartment, $request->all());

        return new DepartmentResource($department);
    }


    /**
     * @param SubDepartment $subDepartment
     * @return JsonResponse
     */
    public function destroy(SubDepartment $subDepartment): JsonResponse
    {
        $this->subDepartmentRepository->delete($subDepartment);

        return response()->json(null, 204);
    }
}
