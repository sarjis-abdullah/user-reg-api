<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerLoyaltyReward\IndexRequest;
use App\Http\Resources\CustomerLoyaltyRewardResource;
use App\Models\CustomerLoyaltyReward;
use App\Repositories\Contracts\CustomerLoyaltyRewardRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerLoyaltyRewardController extends Controller
{
    /**
     * @var CustomerLoyaltyRewardRepository
     */
    protected $customerLoyaltyRewardRepository;

    /**
     * @param CustomerLoyaltyRewardRepository $customerLoyaltyRewardRepository
     */
    public function __construct(CustomerLoyaltyRewardRepository $customerLoyaltyRewardRepository)
    {
        $this->customerLoyaltyRewardRepository = $customerLoyaltyRewardRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $loyaltyRewards = $this->customerLoyaltyRewardRepository->findBy($request->all());

        return CustomerLoyaltyRewardResource::collection($loyaltyRewards);
    }

    /**
     * Display the specified resource.
     *
     * @param CustomerLoyaltyReward $customerLoyaltyReward
     * @return CustomerLoyaltyRewardResource
     */
    public function show(CustomerLoyaltyReward $customerLoyaltyReward): CustomerLoyaltyRewardResource
    {
        return new CustomerLoyaltyRewardResource($customerLoyaltyReward);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CustomerLoyaltyReward $customerLoyaltyReward
     * @return JsonResponse
     */
    public function destroy(CustomerLoyaltyReward $customerLoyaltyReward): JsonResponse
    {
        $this->customerLoyaltyRewardRepository->delete($customerLoyaltyReward);

        return response()->json(null, 204);
    }
}
