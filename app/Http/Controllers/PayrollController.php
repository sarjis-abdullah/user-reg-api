<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payroll\IndexRequest;
use App\Http\Requests\Payroll\StoreRequest;
use App\Http\Requests\Payroll\UpdateRequest;
use App\Http\Resources\PayrollResource;
use App\Models\Payroll;
use App\Repositories\Contracts\PayrollRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PayrollController extends Controller
{
     /**
     * @var PayrollRepository
     */
    protected $PayrollRepository;

    /**
     * ProductController constructor.
     * @param PayrollRepository $PayrollRepository
     */
    public function __construct(PayrollRepository $PayrollRepository)
    {
        $this->PayrollRepository = $PayrollRepository;
    }

    /**
    * Display a listing of the resource.
    *
    * @param IndexRequest $request
    * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $Payroll = $this->PayrollRepository->findBy($request->all());

        return PayrollResource::collection($Payroll);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return PayrollResource
     */
    public function store(StoreRequest $request)
    {
        $Payroll = $this->PayrollRepository->save($request->all());

        return new PayrollResource($Payroll);
    }

    /**
     * Display the specified resource.
     *
     * @param Payroll $Payroll
     * @return PayrollResource
     */
    public function show(Payroll $Payroll)
    {
        return new PayrollResource($Payroll);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Payroll $Payroll
     * @return PayrollResource
     */
    public function update(UpdateRequest $request, Payroll $Payroll)
    {
        $Payroll = $this->PayrollRepository->update($Payroll, $request->all());

        return new PayrollResource($Payroll);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Payroll $Payroll
     * @return JsonResponse
     */
    public function destroy(Payroll $Payroll)
    {
        $this->PayrollRepository->delete($Payroll);

        return \response()->json(null, 204);
    }

}
