<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashUp\StoreRequest;
use App\Http\Requests\CashUp\IndexRequest;
use App\Http\Requests\CashUp\UpdateRequest;
use App\Http\Resources\CashUpResource;
use App\Models\CashUp;
use App\Repositories\Contracts\CashUpRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CashUpController extends Controller
{
    /**
     * @var CashUpRepository
     */
    protected $cashUpRepository;

    /**
     * BrandController constructor.
     * @param CashUpRepository $cashUpRepository
     */
    public function __construct(CashUpRepository $cashUpRepository)
    {
        $this->cashUpRepository = $cashUpRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $cashUps = $this->cashUpRepository->findBy($request->all());

        return CashUpResource::collection($cashUps);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return CashUpResource
     */
    public function store(StoreRequest $request)
    {
        $cashUp = $this->cashUpRepository->save($request->all());

        return new CashUpResource($cashUp);
    }

    /**
     * Display the specified resource.
     *
     * @param CashUp $cashUp
     * @return CashUpResource
     */
    public function show(CashUp $cashUp)
    {
        return new CashUpResource($cashUp);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param CashUp $cashUp
     * @return CashUpResource
     */
    public function update(UpdateRequest $request, CashUp $cashUp)
    {
        $cashUp = $this->cashUpRepository->update($cashUp, $request->all());

        return new CashUpResource($cashUp);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CashUp $cashUp
     * @return JsonResponse
     */
    public function destroy(CashUp $cashUp)
    {
        $this->cashUpRepository->delete($cashUp);

        return \response()->json(null, 204);
    }
}
