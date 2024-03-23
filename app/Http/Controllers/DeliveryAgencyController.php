<?php

namespace App\Http\Controllers;
use App\Http\Requests\DeliveryAgency\IndexRequest;
use App\Http\Requests\DeliveryAgency\StoreRequest;
use App\Http\Requests\DeliveryAgency\UpdateRequest;
use App\Http\Resources\DeliveryAgencyResource;
use App\Models\DeliveryAgency;
use App\Repositories\Contracts\DeliveryAgencyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeliveryAgencyController extends Controller
{
    /**
     * @var DeliveryAgencyRepository
     */
    protected $deliverAgencyRepository;

    /**
     * ProductController constructor.
     * @param DeliveryAgencyRepository $deliveryAgencyRepository
     */
    public function __construct(DeliveryAgencyRepository $deliveryAgencyRepository)
    {
        $this->deliverAgencyRepository = $deliveryAgencyRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
//        $this->authorize('list', DeliveryAgency::class);

        $deliveryAgency = $this->deliverAgencyRepository->findBy($request->all());

        return DeliveryAgencyResource::collection($deliveryAgency);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreRequest $request
     * @return DeliveryAgencyResource
     */
    public function store(StoreRequest $request)
    {
//        $this->authorize('store', [DeliveryAgency::class]);

        $deliveryAgency = $this->deliverAgencyRepository->save($request->all());

        return new DeliveryAgencyResource($deliveryAgency);
    }

    /**
     * Display the specified resource.
     *
     * @param DeliveryAgency $deliveryAgency
     * @return DeliveryAgencyResource
     */
    public function show(DeliveryAgency $deliveryAgency): DeliveryAgencyResource
    {
//        $this->authorize('show', $deliveryAgency);

        return new DeliveryAgencyResource($deliveryAgency);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param DeliveryAgency $deliveryAgency
     * @return DeliveryAgencyResource
     */
    public function update(UpdateRequest $request, DeliveryAgency $deliveryAgency)
    {
//        $this->authorize('update', $deliveryAgency);

        $deliveryAgency = $this->deliverAgencyRepository->update($deliveryAgency, $request->all());

        return new DeliveryAgencyResource($deliveryAgency);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeliveryAgency $deliveryAgency
     * @return JsonResponse
     */
    public function destroy(DeliveryAgency $deliveryAgency)
    {
//        $this->authorize('destroy', $deliveryAgency);

        $this->deliverAgencyRepository->delete($deliveryAgency);

        return \response()->json(null, 204);
    }
}
