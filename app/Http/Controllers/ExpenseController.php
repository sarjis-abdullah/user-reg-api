<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expense\IndexRequest;
use App\Http\Requests\Expense\StoreRequest;
use App\Http\Requests\Expense\UpdateRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Repositories\Contracts\ExpenseRepository;
use App\Services\Helpers\PdfHelper;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    /**
     * @var ExpenseRepository
     */
    protected $expenseRepository;

    /**
     * @param ExpenseRepository $expenseRepository
     */
    public function __construct(ExpenseRepository $expenseRepository)
    {
        $this->expenseRepository = $expenseRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $expenseRepo = $this->expenseRepository->findBy($request->all());

        $expenseResources = ExpenseResource::collection($expenseRepo['expenses']);

        $expenseResources->additional(Arr::except($expenseRepo, ['expenses']));

        return $expenseResources;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return ExpenseResource
     */
    public function store(StoreRequest $request)
    {
        $expense = $this->expenseRepository->save($request->all());

        return new ExpenseResource($expense);
    }

    /**
     * Display the specified resource.
     *
     * @param Expense $expense
     * @return ExpenseResource
     */
    public function show(Expense $expense)
    {
        return new ExpenseResource($expense);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Expense $expense
     * @return ExpenseResource|\Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function update(UpdateRequest $request, Expense $expense)
    {
        if(!$expense->created_at->isToday()) {
            return response()->json((['status' => 403, 'message' => "You can't update expense."]), 403);
        }

        $expense = $this->expenseRepository->update($expense, $request->all());

        return new ExpenseResource($expense);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Expense $expense
     * @return JsonResponse
     */
    public function destroy(Expense $expense)
    {
        $this->expenseRepository->delete($expense);

        return response()->json(null, 204);
    }

    /**
     * Expanse Report
     *
     * @param IndexRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function expensePdf(IndexRequest $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $expenseRepo = $this->expenseRepository->findBy($request->all());

        $expenseResources = ExpenseResource::collection($expenseRepo['expenses']);

        $expenseResources->additional(Arr::except($expenseRepo, ['expenses']));

        return PdfHelper::downloadPdf(json_encode($expenseResources), 'pdf.reports.expanse', 'Expanse-report.pdf');
    }
}
