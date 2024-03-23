<?php

namespace App\Http\Controllers;
use App\Exports\StockTransferReportExport;
use App\Http\Requests\StockTransfer\IndexRequest;
use App\Http\Requests\StockTransfer\StoreRequest;
use App\Http\Requests\StockTransfer\UpdateRequest;
use App\Http\Resources\StockTransferResource;
use App\Models\StockTransfer;
use App\Repositories\Contracts\StockTransferRepository;
use App\Services\Helpers\PdfHelper;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockTransferController extends Controller
{
    /**
     * @var StockTransferRepository
     */
    protected $stockTransferRepository;

    /**
     * ProductController constructor.
     * @param StockTransferRepository $stockTransferRepository
     */
    public function __construct(StockTransferRepository $stockTransferRepository)
    {
        $this->stockTransferRepository = $stockTransferRepository;
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
        $this->authorize('list', [StockTransfer::class, 'stock_transfer_list']);

        $stockTransfers = $this->stockTransferRepository->findBy($request->all());

        $resources = StockTransferResource::collection($stockTransfers['result']);

        $resources->additional(['summary' => $stockTransfers['summary'], 'pageWiseSummary' => $stockTransfers['pageWiseSummary']]);

        return $resources;
    }

    /**
     * @param IndexRequest $request
     * @return StreamedResponse
     * @throws AuthorizationException
     * @throws MpdfException
     */
    public function stockTransferPdf(IndexRequest $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->authorize('list', [StockTransfer::class, 'stock_transfer_list']);

        $stockTransfers = $this->stockTransferRepository->findBy($request->all());

        $data = StockTransferResource::collection($stockTransfers['result']);

        return PdfHelper::downloadPdf(json_encode($data),'pdf.reports.stockTransferReport', 'Stock-transfer-report.pdf');
    }

    /**
     * @param IndexRequest $request
     * @return BinaryFileResponse
     * @throws AuthorizationException
     */
    public function stockTransferExcel(IndexRequest $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorize('list', [StockTransfer::class, 'stock_transfer_list']);

        $stockTransfers = $this->stockTransferRepository->findBy($request->all());

        $data = StockTransferResource::collection($stockTransfers['result']);

        return Excel::download(new StockTransferReportExport($data), 'Stock-transfer-report.xlsx');
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreRequest $request
     * @return StockTransferResource
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('store', [StockTransfer::class, 'stock_transfer_create']);

        $stockTransfers = $this->stockTransferRepository->save($request->all());

        return new StockTransferResource($stockTransfers);
    }

    /**
     * Display the specified resource.
     *
     * @param StockTransfer $stockTransfer
     * @return StockTransferResource
     */
    public function show(StockTransfer $stockTransfer): StockTransferResource
    {
        $this->authorize('show', [$stockTransfer, 'stock_transfer_view']);

        return new StockTransferResource($stockTransfer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param StockTransfer $stockTransfer
     * @return StockTransferResource
     */
    public function update(UpdateRequest $request, StockTransfer $stockTransfer)
    {
        $this->authorize('update', [$stockTransfer, 'stock_transfer_update']);

        $stockTransfer = $this->stockTransferRepository->updateStockTransfer($stockTransfer, $request->all());

        return new StockTransferResource($stockTransfer);
    }
}
