<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\DuePayRequest;
use App\Http\Requests\Supplier\StoreRequest;
use App\Http\Requests\Supplier\IndexRequest;
use App\Http\Requests\Supplier\UpdateRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepository;
use App\Services\Helpers\PdfHelper;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SupplierController extends Controller
{
    /**
     * @var SupplierRepository
     */
    protected $supplierRepository;

    /**
     * SupplierController constructor.
     * @param SupplierRepository $supplierRepository
     */
    public function __construct(SupplierRepository $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $suppliers = $this->supplierRepository->findBy($request->all());

        $supplierResources = SupplierResource::collection($suppliers['suppliers']);

        unset($suppliers['suppliers']);

        $supplierResources->additional($suppliers);

        return $supplierResources;
    }

    public function supplierReportPdf(IndexRequest $request)
    {
        $suppliers = $this->supplierRepository->findBy($request->all());

//        return $suppliers['suppliers'];
        return PdfHelper::downloadPdf($suppliers['suppliers'], 'pdf.reports.supplierReport', 'Supplier-report.pdf');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return SupplierResource
     */
    public function store(StoreRequest $request)
    {
        $supplier = $this->supplierRepository->save($request->all());

        return new SupplierResource($supplier);
    }

    /**
     * Display the specified resource.
     *
     * @param Supplier $supplier
     * @return SupplierResource
     */
    public function show(Supplier $supplier)
    {
        return new SupplierResource($supplier);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Supplier $supplier
     * @return SupplierResource
     */
    public function update(UpdateRequest $request, Supplier $supplier)
    {
        $supplier = $this->supplierRepository->update($supplier, $request->all());

        return new SupplierResource($supplier);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Supplier $supplier
     * @return JsonResponse
     */
    public function destroy(Supplier $supplier)
    {
        $this->supplierRepository->delete($supplier);

        return response()->json(null, 204);
    }

    /**
     * @param DuePayRequest $request
     * @return JsonResponse
     */
    public function supplierDuePay(DuePayRequest $request): JsonResponse
    {
        $this->supplierRepository->paySupplierDue($request->all());

        return response()->json(null, 200);
    }
}
