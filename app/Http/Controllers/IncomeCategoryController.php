<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncomeCategory\IndexRequest;
use App\Http\Requests\IncomeCategory\StoreRequest;
use App\Http\Requests\IncomeCategory\UpdateRequest;
use App\Http\Resources\IncomeCategoryResource;
use App\Models\IncomeCategory;
use App\Repositories\Contracts\IncomeCategoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IncomeCategoryController extends Controller
{

    /**
     * @var IncomeCategoryRepository
     */
    private $repository;

    public function __construct(IncomeCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $items = $this->repository->findBy($request->all());

        return IncomeCategoryResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return IncomeCategoryResource
     */
    public function store(StoreRequest $request): IncomeCategoryResource
    {
        $item = $this->repository->save($request->all());

        return new IncomeCategoryResource($item);
    }

    /**
     * Display the specified resource.
     *
     * @param IncomeCategory $incomeCategory
     * @return IncomeCategoryResource
     */
    public function show(IncomeCategory $incomeCategory): IncomeCategoryResource
    {
        return new IncomeCategoryResource($incomeCategory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param IncomeCategory $incomeCategory
     * @return IncomeCategoryResource
     */
    public function update(UpdateRequest $request, IncomeCategory $incomeCategory): IncomeCategoryResource
    {
        $item = $this->repository->update($incomeCategory, $request->all());

        return new IncomeCategoryResource($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param IncomeCategory $incomeCategory
     * @return JsonResponse
     */
    public function destroy(IncomeCategory $incomeCategory): JsonResponse
    {
        $this->repository->delete($incomeCategory);

        return response()->json(null, 204);
    }
}
