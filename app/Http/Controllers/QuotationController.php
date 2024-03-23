<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quotation\IndexRequest;
use App\Http\Requests\Quotation\StoreRequest;
use App\Http\Requests\Quotation\UpdateRequest;
use App\Http\Resources\QuotationResource;
use App\Models\Quotation;
use App\Repositories\Contracts\QuotationRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuotationController extends Controller
{
    protected $quotationRepository;

    /**
     * QuotationController constructor.
     * @param QuotationRepository $quotationRepository
     */
    public function __construct(QuotationRepository $quotationRepository)
    {
        $this->quotationRepository = $quotationRepository;
    }

    /**
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
       $quotation = $this->quotationRepository->findBy($request->all());

       return QuotationResource::collection($quotation);
    }

    /**
     * @param Quotation $quotation
     * @return QuotationResource
     */
    public function show(Quotation $quotation)
    {
        return new QuotationResource($quotation);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest  $request
     * @return \ArrayAccess
     */
    public function store(StoreRequest $request)
    {
        $quotation = $this->quotationRepository->save($request->all());

        return new QuotationResource($quotation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateQuotationRequest  $request
     * @param  \App\Models\Quotation  $quotation
     * @return QuotationResource
     */
    public function update(UpdateRequest $request, Quotation $quotation)
    {
        $item = $this->quotationRepository->update($quotation, $request->all());

        return new QuotationResource($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Quotation  $quotation
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Quotation $quotation)
    {
        $this->quotationRepository->delete($quotation);

        return response()->json(null, 204);
    }
}
