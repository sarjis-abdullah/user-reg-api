<?php

namespace App\Http\Controllers;

use App\Http\Requests\Coupon\CouponValidation;
use App\Http\Requests\Coupon\IndexRequest;
use App\Http\Requests\Coupon\StoreRequest;
use App\Http\Requests\Coupon\UpdateRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CouponController extends Controller
{
    /**
     * @var CouponRepository
     */
    protected $couponRepository;

    /**
     * @param CouponRepository $couponRepository
     */
    public function __construct(CouponRepository $couponRepository)
    {
        $this->couponRepository = $couponRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $coupons = $this->couponRepository->findBy($request->all());

        return CouponResource::collection($coupons);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return CouponResource
     */
    public function store(StoreRequest $request): CouponResource
    {
        $coupon = $this->couponRepository->save($request->all());

        return new CouponResource($coupon);
    }

    /**
     * Display the specified resource.
     *
     * @param Coupon $coupon
     * @return CouponResource
     */
    public function show(Coupon $coupon): CouponResource
    {
        return new CouponResource($coupon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Coupon $coupon
     * @return CouponResource
     */
    public function update(UpdateRequest $request, Coupon $coupon): CouponResource
    {
        $coupon = $this->couponRepository->update($coupon, $request->all());

        return new CouponResource($coupon);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Coupon $coupon
     * @return JsonResponse
     */
    public function destroy(Coupon $coupon): JsonResponse
    {
        $this->couponRepository->delete($coupon);

        return \response()->json(null, 204);
    }

    /**
     * @param CouponValidation $request
     * @return CouponResource
     */
    public function couponValidation(CouponValidation $request): CouponResource
    {
        $coupon = $this->couponRepository->findOneBy(['code' => $request->get('code')]);

        return new CouponResource($coupon);
    }
}
