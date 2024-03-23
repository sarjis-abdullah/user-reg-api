<?php

namespace App\Http\Controllers;

use App\Http\Requests\CouponCustomer\IndexRequest;
use App\Http\Resources\CouponCustomerResource;
use App\Models\CouponCustomer;
use App\Repositories\Contracts\CouponCustomerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CouponCustomerController extends Controller
{
    /**
     * @var CouponCustomerRepository
     */
    protected $couponCustomerRepository;

    /**
     * @param CouponCustomerRepository $couponCustomerRepository
     */
    public function __construct(CouponCustomerRepository $couponCustomerRepository)
    {
        $this->couponCustomerRepository = $couponCustomerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $couponCustomers = $this->couponCustomerRepository->findBy($request->all());

        return CouponCustomerResource::collection($couponCustomers);
    }


    /**
     * Display the specified resource.
     *
     * @param CouponCustomer $couponCustomer
     * @return CouponCustomerResource
     */
    public function show(CouponCustomer $couponCustomer): CouponCustomerResource
    {
        return new CouponCustomerResource($couponCustomer);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param CouponCustomer $couponCustomer
     * @return JsonResponse
     */
    public function destroy(CouponCustomer $couponCustomer): JsonResponse
    {
        $this->couponCustomerRepository->delete($couponCustomer);

        return \response()->json(null, 204);
    }
}
