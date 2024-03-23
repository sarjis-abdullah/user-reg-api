<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderLog\IndexRequest;
use App\Http\Resources\OrderLogResource;
use App\Models\Order;
use App\Repositories\Contracts\OrderLogRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderLogController extends Controller
{
    /**
     * @var OrderLogRepository
     */
    protected OrderLogRepository $orderLogRepository;

    public function __construct(OrderLogRepository $orderLogRepository)
    {
        $this->orderLogRepository = $orderLogRepository;
    }

    /**
     * @param IndexRequest $request
     * @param Order $order
     * @return AnonymousResourceCollection
     */
    protected function index(IndexRequest $request, Order $order): AnonymousResourceCollection
    {
        $orderLogs = $this->orderLogRepository->findBy($request->all());

        return OrderLogResource::collection($orderLogs);
    }
}
