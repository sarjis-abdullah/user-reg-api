<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseProduct\IndexRequest;
use App\Http\Resources\PurchaseProductResource;
use App\Models\PurchaseProduct;
use App\Repositories\Contracts\PurchaseProductRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PurchaseProductController extends Controller
{
    protected $purchaseProductRepository;

    /**
     * ProductController constructor.
     * @param PurchaseProductRepository $purchaseProductRepository
     */
    public function __construct(PurchaseProductRepository $purchaseProductRepository)
    {
        $this->purchaseProductRepository = $purchaseProductRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $purchaseProducts = $this->purchaseProductRepository->findBy($request->all());

        return PurchaseProductResource::collection($purchaseProducts);
    }

    /**
     * Display the specified resource.
     *
     * @param PurchaseProduct $purchaseProduct
     * @return PurchaseProductResource
     */
    public function show(PurchaseProduct $purchaseProduct)
    {
        return new PurchaseProductResource($purchaseProduct);
    }
}
