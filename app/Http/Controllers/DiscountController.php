<?php

namespace App\Http\Controllers;

use App\Http\Requests\Discount\IndexRequest;
use App\Http\Requests\Discount\StoreRequest;
use App\Http\Requests\Discount\UpdateRequest;
use App\Http\Resources\DiscountResource;
use App\Models\Discount;
use App\Repositories\Contracts\DiscountRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DiscountController extends Controller
{
    /**
     * @var DiscountRepository
     */
    protected $discountRepository;

    /**
     * DiscountController constructor.
     * @param DiscountRepository $discountRepository
     */
    public function __construct(DiscountRepository $discountRepository) {
        $this->discountRepository = $discountRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $discounts = $this->discountRepository->findBy($request->all());

        return DiscountResource::collection($discounts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return DiscountResource
     */
    public function store(StoreRequest $request)
    {
        $discount = $this->discountRepository->save($request->all());

        return new DiscountResource($discount);
    }

    /**
     * Display the specified resource.
     *
     * @param Discount $discount
     * @return DiscountResource
     */
    public function show(Discount $discount)
    {
        return new DiscountResource($discount);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Discount $discount
     * @return DiscountResource
     */
    public function update(UpdateRequest $request, Discount $discount)
    {
        $discount = $this->discountRepository->update($discount, $request->all());

        return new DiscountResource($discount);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Discount $discount
     * @return JsonResponse
     */
    public function destroy(Discount $discount)
    {
        $this->discountRepository->delete($discount);

        return response()->json(null, 204);
    }
}
