<?php

namespace App\Http\Controllers;

use App\Http\Requests\Brand\StoreRequest;
use App\Http\Requests\Brand\IndexRequest;
use App\Http\Requests\Brand\UpdateRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Repositories\Contracts\BrandRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BrandController extends Controller
{
    /**
     * @var BrandRepository
     */
    protected $brandRepository;

    /**
     * BrandController constructor.
     * @param BrandRepository $brandRepository
     */
    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $brands = $this->brandRepository->findBy($request->all());

        return BrandResource::collection($brands);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return BrandResource
     */
    public function store(StoreRequest $request)
    {
        $brand = $this->brandRepository->save($request->all());

        return new BrandResource($brand);
    }

    /**
     * Display the specified resource.
     *
     * @param Brand $brand
     * @return BrandResource
     */
    public function show(Brand $brand)
    {
        return new BrandResource($brand);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Brand $brand
     * @return BrandResource
     */
    public function update(UpdateRequest $request, Brand $brand)
    {
        $brand = $this->brandRepository->update($brand, $request->all());

        return new BrandResource($brand);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Brand $brand
     * @return JsonResponse
     */
    public function destroy(Brand $brand)
    {
        $this->brandRepository->delete($brand);

        return \response()->json(null, 204);
    }
}
