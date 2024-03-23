<?php

namespace App\Http\Controllers;

use App\Http\Requests\EcomIntegration\IndexRequest;
use App\Http\Requests\EcomIntegration\StoreRequest;
use App\Http\Requests\EcomIntegration\UpdateRequest;
use App\Http\Resources\EcomIntegrationResource;
use App\Models\EcomIntegration;
use App\Repositories\Contracts\EcomIntegrationRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EcomIntegrationController extends Controller
{
    /**
     * @var EcomIntegrationRepository
     */
    protected $ecomIntegrationRepository;

    /**
     * @param EcomIntegrationRepository $ecomIntegrationRepository
     */
    public function __construct(EcomIntegrationRepository $ecomIntegrationRepository)
    {
        $this->ecomIntegrationRepository = $ecomIntegrationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $this->authorize('list', EcomIntegration::class);

        $ecomIntegrations = $this->ecomIntegrationRepository->findBy($request->all());

        return EcomIntegrationResource::collection($ecomIntegrations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return EcomIntegrationResource
     * @throws AuthorizationException
     */
    public function store(StoreRequest $request): EcomIntegrationResource
    {
        $this->authorize('store', EcomIntegration::class);

        $ecomIntegration = $this->ecomIntegrationRepository->save($request->all());

        return new EcomIntegrationResource($ecomIntegration);
    }

    /**
     * Display the specified resource.
     *
     * @param EcomIntegration $ecomIntegration
     * @return EcomIntegrationResource
     * @throws AuthorizationException
     */
    public function show(EcomIntegration $ecomIntegration): EcomIntegrationResource
    {
        $this->authorize('show', $ecomIntegration);

        return new EcomIntegrationResource($ecomIntegration);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param EcomIntegration $ecomIntegration
     * @return EcomIntegrationResource
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, EcomIntegration $ecomIntegration): EcomIntegrationResource
    {
        $this->authorize('update', $ecomIntegration);

        $ecomIntegration = $this->ecomIntegrationRepository->update($ecomIntegration, $request->all());

        return new EcomIntegrationResource($ecomIntegration);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EcomIntegration $ecomIntegration
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(EcomIntegration $ecomIntegration): JsonResponse
    {
        $this->authorize('destroy', $ecomIntegration);

        $this->ecomIntegrationRepository->delete($ecomIntegration);

        return response()->json(null, 204);
    }
}
