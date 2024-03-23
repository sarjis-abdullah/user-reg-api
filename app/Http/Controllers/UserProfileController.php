<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Http\Requests\UserProfile\IndexRequest;
use App\Http\Requests\UserProfile\StoreRequest;
use App\Http\Requests\UserProfile\UpdateRequest;
use App\Http\Resources\UserProfileResource;
use App\Repositories\Contracts\UserProfileRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserProfileController extends Controller
{
    /**
     * @var UserProfileRepository
     */
    protected $userProfileRepository;

    /**
     * UserProfileController constructor.
     * @param UserProfileRepository $userProfileRepository
     */
    public function __construct(UserProfileRepository $userProfileRepository)
    {
        $this->userProfileRepository = $userProfileRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $this->authorize('list', [UserProfile::class, $request->get('userId')]);

        $userProfiles = $this->userProfileRepository->findBy($request->all());

        return UserProfileResource::collection($userProfiles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return UserProfileResource
     * @throws AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('store', [UserProfile::class, $request->get('userId')]);

        $userProfile = $this->userProfileRepository->save($request->all());

        return new UserProfileResource($userProfile);
    }

    /**
     * Display the specified resource.
     *
     * @param UserProfile $userProfile
     * @return UserProfileResource
     * @throws AuthorizationException
     */
    public function show(UserProfile $userProfile)
    {
        $this->authorize('show', $userProfile);

        return new UserProfileResource($userProfile);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param UserProfile $userProfile
     * @return UserProfileResource
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, UserProfile $userProfile)
    {
        $this->authorize('update', $userProfile);

        $userProfile = $this->userProfileRepository->update($userProfile, $request->all());

        return new UserProfileResource($userProfile);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param UserProfile $userProfile
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(UserProfile $userProfile)
    {
        $this->authorize('destroy', $userProfile);

        $this->userProfileRepository->delete($userProfile);

        return response()->json(null,203);
    }
}
