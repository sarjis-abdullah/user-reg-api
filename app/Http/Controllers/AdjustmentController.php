<?php

namespace App\Http\Controllers;

use App\Http\Requests\Adjustment\IndexRequest;
use App\Http\Requests\Adjustment\StoreRequest;
use App\Http\Requests\Adjustment\UpdateRequest;
use App\Http\Resources\AdjustmentResource;
use App\Models\Adjustment;
use App\Repositories\Contracts\AdjustmentRepository;
use App\Services\Helpers\PdfHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdjustmentController extends Controller
{
    /**
     * @var AdjustmentRepository
     */
    private $repository;

    public function __construct(AdjustmentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $items = $this->repository->findBy($request->all());

        return AdjustmentResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return AnonymousResourceCollection
     */
    public function store(StoreRequest $request): AnonymousResourceCollection
    {
        $items = $this->repository->save($request->all());

        return AdjustmentResource::collection($items);
    }

    /**
     * Display the specified resource.
     *
     * @param Adjustment $adjustment
     * @return AdjustmentResource
     */
    public function show(Adjustment $adjustment): AdjustmentResource
    {
        return new AdjustmentResource($adjustment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Adjustment $adjustment
     * @return AdjustmentResource
     */
    public function update(UpdateRequest $request, Adjustment $adjustment): AdjustmentResource
    {
        $item = $this->repository->update($adjustment, $request->all());

        return new AdjustmentResource($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Adjustment $adjustment
     * @return JsonResponse
     */
    public function destroy(Adjustment $adjustment): JsonResponse
    {
        $this->repository->delete($adjustment);

        return response()->json(null, 204);
    }

    /**
     * @param IndexRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function downloadAdjustmentPdf(IndexRequest $request): StreamedResponse
    {
        $items = $this->repository->findBy($request->all());

        $adjustmentResource = AdjustmentResource::collection($items);

         return PdfHelper::downloadPdf($adjustmentResource, 'pdf.adjustment.adjustmentList', 'Adjustment-list');
    }
}
