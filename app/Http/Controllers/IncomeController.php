<?php

namespace App\Http\Controllers;

use App\Http\Requests\Income\IndexRequest;
use App\Http\Requests\Income\StoreRequest;
use App\Http\Requests\Income\UpdateRequest;
use App\Http\Resources\IncomeResource;
use App\Models\Income;
use App\Repositories\Contracts\IncomeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;

class IncomeController extends Controller
{
    /**
     * @var IncomeRepository
     */
    private $incomeRepository;

    public function __construct(IncomeRepository $incomeRepository)
    {
        $this->incomeRepository = $incomeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $incomesRepo = $this->incomeRepository->findBy($request->all());

        $incomeResources = IncomeResource::collection( $incomesRepo['incomes']);

        $incomeResources->additional(Arr::except($incomesRepo, ['incomes']));

        return $incomeResources;
    }

    /**
     * Store a newly created resource in storage
     * @param StoreRequest $request
     * @return IncomeResource
     */
    public function store(StoreRequest $request): IncomeResource
    {
        $item = $this->incomeRepository->save($request->all());

        return new IncomeResource($item);
    }

    /**
     * Display the specified resource.
     *
     * @param Income $income
     * @return IncomeResource
     */
    public function show(Income $income): IncomeResource
    {
        return new IncomeResource($income);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Income $income
     * @return IncomeResource
     */
    public function update(UpdateRequest $request, Income $income): IncomeResource
    {
        $item = $this->incomeRepository->update($income, $request->all());

        return new IncomeResource($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Income $income
     * @return JsonResponse
     */
    public function destroy(Income $income): JsonResponse
    {
        $this->incomeRepository->delete($income);

        return response()->json(null, 204);
    }
}
