<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderProductReturn\IndexRequest;
use App\Http\Requests\OrderProductReturn\StoreRequest;
use App\Http\Resources\OrderProductReturnResource;
use App\Models\OrderProductReturn;
use App\Repositories\Contracts\OrderProductReturnRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderProductReturnController extends Controller
{
    protected $orderProductReturnRepository;

    /**
     * ProductController constructor.
     * @param OrderProductReturnRepository $orderProductReturnRepository
     */
    public function __construct(OrderProductReturnRepository $orderProductReturnRepository)
    {
        $this->orderProductReturnRepository = $orderProductReturnRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $orderReturnProducts = $this->orderProductReturnRepository->findBy($request->all());

        $orderReturnProductsResources =  OrderProductReturnResource::collection(!empty($orderReturnProducts['summary']) ? $orderReturnProducts['returnOrders'] : $orderReturnProducts);

        if (!empty($orderReturnProducts['summary'])) {
            $orderReturnProductsResources->additional(['summary' => $orderReturnProducts['summary']]);
        }

        return $orderReturnProductsResources;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return AnonymousResourceCollection
     */
    public function store(StoreRequest $request): AnonymousResourceCollection
    {
        $purchaseProductReturns = $this->orderProductReturnRepository->save($request->all());

        return OrderProductReturnResource::collection($purchaseProductReturns);
    }

    /**
     * Display the specified resource.
     *
     * @param OrderProductReturn $orderProductReturn
     * @return OrderProductReturnResource
     */
    public function show(OrderProductReturn $orderProductReturn)
    {
        return new OrderProductReturnResource($orderProductReturn);
    }
}
