<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStockSerial\IndexRequest;
use App\Http\Resources\ProductStockSerialResource;
use App\Models\ProductStockSerial;
use App\Repositories\Contracts\ProductStockSerialRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductStockSerialController extends Controller
{
    protected $productStockSerialRepository;

    /**
     * @param ProductStockSerialRepository $productStockSerialRepository
     */
    public function __construct(ProductStockSerialRepository $productStockSerialRepository)
    {
        $this->productStockSerialRepository = $productStockSerialRepository;
    }


    /**
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $resource = $this->productStockSerialRepository->findBy($request->all());

        return ProductStockSerialResource::collection($resource);
    }

    /**
     * Display the specified resource.
     *
     * @param ProductStockSerial $productStockSerial
     * @return ProductStockSerialResource
     */
    public function show(ProductStockSerial $productStockSerial): ProductStockSerialResource
    {
        return new ProductStockSerialResource($productStockSerial);
    }
}
