<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderProduct\IndexRequest;
use App\Http\Resources\OrderProductResource;
use App\Models\OrderProduct;
use App\Repositories\Contracts\OrderProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderProductController extends Controller
{
    protected $orderProductRepository;

    /**
     * ProductController constructor.
     * @param OrderProductRepository $orderProductRepository
     */
    public function __construct(OrderProductRepository $orderProductRepository)
    {
        $this->orderProductRepository = $orderProductRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $productSaleDetails = $this->orderProductRepository->findBy($request->all());

        return OrderProductResource::collection($productSaleDetails);
    }

    /**
     * Display the specified resource.
     *
     * @param OrderProduct $productSaleDetail
     * @return OrderProductResource
     */
    public function show(OrderProduct $productSaleDetail)
    {
        return new OrderProductResource($productSaleDetail);
    }

    /**
     * @param int $id
     * @return OrderProductResource
     */
    public function revertWrongStockSale(int $id): OrderProductResource
    {
        //TODO: to revert wrong sale and update branch and stock profit.
        $orderProduct = $this->orderProductRepository->findOne($id);

        return new OrderProductResource($orderProduct);
    }
}
