<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employee\AssignToManagerRequest;
use App\Http\Requests\Employee\IndexRequest;
use App\Http\Requests\Employee\UpdateRequest;
use App\Http\Requests\Employee\StoreRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Repositories\Contracts\EmployeeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeController extends Controller
{
    /**
     * @var EmployeeRepository
     */
    protected $employeeRepository;

    /**
     * EmployeeController constructor.
     * @param EmployeeRepository $employeeRepository
     */
    public function __construct(EmployeeRepository $employeeRepository) {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $employees = $this->employeeRepository->findBy($request->all());

        return EmployeeResource::collection($employees);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return EmployeeResource
     */
    public function store(StoreRequest $request)
    {
        $employee = $this->employeeRepository->save($request->all());

        return new EmployeeResource($employee);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AssignToManagerRequest $request
     * @return EmployeeResource
     */
    public function employeeAssignToManager(AssignToManagerRequest $request): EmployeeResource
    {
        $employee = $this->employeeRepository->employeeAssignToManager($request->all());

        return new EmployeeResource($employee);
    }

    /**
     * Display the specified resource.
     *
     * @param Employee $employee
     * @return EmployeeResource
     */
    public function show(Employee $employee)
    {
        return new EmployeeResource($employee);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Employee $employee
     * @return EmployeeResource
     */
    public function update(UpdateRequest $request, Employee $employee)
    {
        $employee = $this->employeeRepository->update($employee, $request->all());

        return new EmployeeResource($employee);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Employee $employee
     * @return JsonResponse
     */
    public function destroy(Employee $employee)
    {
        $this->employeeRepository->delete($employee);

        return response()->json(null, 204);
    }
}
