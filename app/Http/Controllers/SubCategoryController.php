<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubCategory\IndexRequest;
use App\Http\Requests\SubCategory\StoreRequest;
use App\Http\Requests\SubCategory\UpdateRequest;
use App\Http\Resources\SubCategoryResource;
use App\Models\SubCategory;
use App\Repositories\Contracts\SubCategoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubCategoryController extends Controller
{
    /**
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * CategoryController constructor.
     * @param SubCategoryRepository $subCategoryRepository
     */
    public function __construct(SubCategoryRepository $subCategoryRepository)
    {
        $this->subCategoryRepository = $subCategoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $categories = $this->subCategoryRepository->findBy($request->all());

        return SubCategoryResource::collection($categories);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return SubCategoryResource
     */
    public function store(StoreRequest $request)
    {
        $subCategory = $this->subCategoryRepository->save($request->all());

        return new SubCategoryResource($subCategory);
    }

    /**
     * Display the specified resource.
     *
     * @param SubCategory $subCategory
     * @return SubCategoryResource
     */
    public function show(SubCategory $subCategory)
    {
        return new SubCategoryResource($subCategory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param SubCategory $subCategory
     * @return SubCategoryResource
     */
    public function update(UpdateRequest $request, SubCategory $subCategory)
    {
        $subCategory = $this->subCategoryRepository->update($subCategory,$request->all());

        return new SubCategoryResource($subCategory);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SubCategory $subCategory
     * @return JsonResponse
     */
    public function destroy(SubCategory $subCategory)
    {
        $this->subCategoryRepository->delete($subCategory);

        return response()->json(null, 204);
    }
}
