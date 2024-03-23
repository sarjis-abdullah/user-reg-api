<?php

namespace App\Http\Controllers;

use App\Http\Requests\Unit\IndexRequest;
use App\Http\Requests\Unit\StoreRequest;
use App\Http\Requests\Unit\UpdateRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use App\Repositories\Contracts\UnitRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UnitController extends Controller
{
    /**
     * @var UnitRepository
     */
    protected $unitRepository;
     /**
     * UnitController constructor.
     * @param UnitRepository $unitRepository
     */
    public function __construct(UnitRepository $unitRepository)
    {
        $this->unitRepository = $unitRepository;
    }
   /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
    */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $units = $this->unitRepository->findBy($request->all());

        return UnitResource::collection($units);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return UnitResource
     */
    public function store(StoreRequest $request)
    {
        $unit = $this->unitRepository->save($request->all());

        return new UnitResource($unit);
    }

    /**
     * Display the specified resource.
     *
     * @param Unit $unit
     * @return UnitResource
     */
    public function show(Unit $unit)
    {
        return new UnitResource($unit);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Unit $unit
     * @return UnitResource
     */
    public function update(UpdateRequest $request, Unit $unit)
    {
        $unit = $this->unitRepository->update($unit, $request->all());

        return new UnitResource($unit);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Unit $unit
     * @return JsonResponse
     */
    public function destroy(Unit $unit)
    {
        $this->unitRepository->delete($unit);

        return response()->json(null, 204);
    }
}
