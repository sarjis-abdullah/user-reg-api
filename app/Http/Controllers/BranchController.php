<?php

namespace App\Http\Controllers;

use App\Http\Requests\Branch\IndexRequest;
use App\Http\Requests\Branch\StoreRequest;
use App\Http\Requests\Branch\UpdateRequest;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use App\Repositories\Contracts\BranchRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BranchController extends Controller
{
    /**
     * @var BranchRepository
     */
    protected $branchRepository;

    /**
     * BranchController constructor.
     * @param BranchRepository $branchRepository
     */
    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
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
        $this->authorize('list', [Branch::class, 'branch_list']);

        $branches = $this->branchRepository->findBy($request->all());

        return BranchResource::collection($branches);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return BranchResource
     * @throws AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('store', Branch::class);

        $branch = $this->branchRepository->save($request->all());

        return new BranchResource($branch);
    }

    /**
     * Display the specified resource.
     *
     * @param Branch $branch
     * @return BranchResource
     * @throws AuthorizationException
     */
    public function show(Branch $branch)
    {
        $this->authorize('show', [$branch, 'branch_view']);

        return new BranchResource($branch);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Branch $branch
     * @return BranchResource
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, Branch $branch)
    {
        $this->authorize('update', $branch);

        $branch = $this->branchRepository->update($branch, $request->all());

        return new BranchResource($branch);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Branch $branch
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Branch $branch)
    {
        $this->authorize('destroy', $branch);

        $this->branchRepository->delete($branch);

        return \response()->json(null, 204);
    }
}
