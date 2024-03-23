<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockLog\IndexRequest;
use App\Http\Requests\StockLog\StoreRequest;
use App\Http\Requests\StockLog\UpdateRequest;
use App\Http\Resources\StockLogResource;
use App\Models\StockLog;
use App\Repositories\Contracts\StockLogRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockLogController extends Controller
{
    /**
     * @var StockLogRepository
     */
    protected $stockLogRepository;

    /**
     * StockLogController constructor.
     * @param StockLogRepository $stockLogRepository
     */
    public function __construct(StockLogRepository $stockLogRepository)
    {
        $this->stockLogRepository = $stockLogRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $stockLogs = $this->stockLogRepository->findBy($request->all());

        return StockLogResource::collection($stockLogs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return StockLogResource
     */
    public function store(StoreRequest $request)
    {
        $stockLog = $this->stockLogRepository->save($request->all());

        return new StockLogResource($stockLog);
    }

    /**
     * Display the specified resource.
     *
     * @param StockLog $stockLog
     * @return StockLogResource
     */
    public function show(StockLog $stockLog)
    {
        return new StockLogResource($stockLog);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param StockLog $stockLog
     * @return StockLogResource
     */
    public function update(UpdateRequest $request, StockLog $stockLog)
    {
        $stockLog = $this->stockLogRepository->update($stockLog, $request->all());

        return new StockLogResource($stockLog);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param StockLog $stockLog
     * @return JsonResponse
     */
    public function destroy(StockLog $stockLog)
    {
        $this->stockLogRepository->delete($stockLog);

        return \response()->json(null, 204);
    }
}
