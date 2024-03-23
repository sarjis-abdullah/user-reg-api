<?php

namespace App\Http\Controllers;

use App\Http\Requests\Department\IndexRequest;
use App\Http\Requests\Department\StoreRequest;
use App\Http\Requests\Department\UpdateRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Repositories\Contracts\DepartmentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DepartmentController extends Controller
{
    protected $departmentRepository;

    /**
     * @param DepartmentRepository $departmentRepository
     */
    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }


    /**
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $departments = $this->departmentRepository->findBy($request->all());

        return DepartmentResource::collection($departments);
    }


    /**
     * @param StoreRequest $request
     * @return DepartmentResource
     */
    public function store(StoreRequest $request): DepartmentResource
    {
        $department = $this->departmentRepository->save($request->all());

        return new DepartmentResource($department);
    }


    /**
     * @param Department $department
     * @return DepartmentResource
     */
    public function show(Department $department): DepartmentResource
    {
        return new DepartmentResource($department);
    }

    /**
     * @param UpdateRequest $request
     * @param Department $department
     * @return DepartmentResource
     */
    public function update(UpdateRequest $request, Department $department): DepartmentResource
    {
        $department = $this->departmentRepository->update($department, $request->all());

        return new DepartmentResource($department);
    }

    /**
     * @param Department $department
     * @return JsonResponse
     */
    public function destroy(Department $department): JsonResponse
    {
        $this->departmentRepository->delete($department);

        return response()->json(null, 204);
    }
}
