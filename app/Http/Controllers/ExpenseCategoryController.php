<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseCategory\IndexRequest;
use App\Http\Requests\ExpenseCategory\StoreRequest;
use App\Http\Requests\ExpenseCategory\UpdateRequest;
use App\Http\Resources\ExpenseCategoryResource;
use App\Models\ExpenseCategory;
use App\Repositories\Contracts\ExpenseCategoryRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExpenseCategoryController extends Controller
{
    /**
     * @var ExpenseCategoryRepository
     */
    protected $expenseCategoryRepository;

    /**
     * @param ExpenseCategoryRepository $expenseCategoryRepository
     */
    public function __construct(ExpenseCategoryRepository $expenseCategoryRepository)
    {
        $this->expenseCategoryRepository = $expenseCategoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $expenseCategories = $this->expenseCategoryRepository->findBy($request->all());

        return ExpenseCategoryResource::collection($expenseCategories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return ExpenseCategoryResource
     */
    public function store(StoreRequest $request)
    {
        $expenseCategory = $this->expenseCategoryRepository->save($request->all());

        return new ExpenseCategoryResource($expenseCategory);
    }

    /**
     * Display the specified resource.
     *
     * @param ExpenseCategory $expenseCategory
     * @return ExpenseCategoryResource
     */
    public function show(ExpenseCategory $expenseCategory)
    {
        return new ExpenseCategoryResource($expenseCategory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param ExpenseCategory $expenseCategory
     * @return ExpenseCategoryResource
     */
    public function update(UpdateRequest $request, ExpenseCategory $expenseCategory)
    {
        $expenseCategory = $this->expenseCategoryRepository->update($expenseCategory, $request->all());

        return new ExpenseCategoryResource($expenseCategory);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ExpenseCategory $expenseCategory
     * @return JsonResponse
     */
    public function destroy(ExpenseCategory $expenseCategory)
    {
        $this->expenseCategoryRepository->delete($expenseCategory);

        return response()->json(null, 204);
    }
}
