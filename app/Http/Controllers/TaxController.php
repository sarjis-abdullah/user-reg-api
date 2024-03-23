<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tax\IndexRequest;
use App\Http\Requests\Tax\StoreRequest;
use App\Http\Requests\Tax\UpdateRequest;
use App\Http\Resources\TaxResource;
use App\Models\Tax;
use App\Repositories\Contracts\TaxRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TaxController extends Controller
{
    /**
     * @var TaxRepository
     */
    private $taxRepository;

    /**
     * @param TaxRepository $taxRepository
     */
    public function __construct(TaxRepository $taxRepository)
    {
        $this->taxRepository = $taxRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $items = $this->taxRepository->findBy($request->all());

        return TaxResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return TaxResource
     */
    public function store(StoreRequest $request): TaxResource
    {
        $item = $this->taxRepository->save($request->all());
        return new TaxResource($item);
    }

    /**
     * Display the specified resource.
     *
     * @param Tax $tax
     * @return TaxResource
     */
    public function show(Tax $tax): TaxResource
    {
        $item = $this->taxRepository->findOne($tax->id);
        return new TaxResource($item);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Tax $tax
     * @return TaxResource
     */
    public function update(UpdateRequest $request, Tax $tax): TaxResource
    {
        $item = $this->taxRepository->update($tax, $request->all());
        return new TaxResource($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tax $tax
     * @return JsonResponse
     */
    public function destroy(Tax $tax): JsonResponse
    {
        $this->taxRepository->delete($tax);

        return response()->json(null, 204);
    }
}
