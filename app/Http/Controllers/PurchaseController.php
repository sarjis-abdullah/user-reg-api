<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseReportExport;
use App\Http\Requests\Purchase\IndexRequest;
use App\Http\Requests\Purchase\StatusUpdateRequest;
use App\Http\Requests\Purchase\StoreRequest;
use App\Http\Requests\Purchase\UpdateRequest;
use App\Http\Resources\PurchaseProductReturnsGroupByDateResource;
use App\Models\Purchase;
use App\Repositories\Contracts\PurchaseRepository;
use App\Http\Resources\PurchaseResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\PurchaseResourceCollection;
use App\Services\Helpers\PdfHelper;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\StreamedResponse;


class PurchaseController extends Controller
{
    /**
     * @var PurchaseRepository
     */
    protected $purchaseRepository;

    /**
     * PurchaseController constructor.
     * @param PurchaseRepository $purchaseRepository
     */
    public function __construct(PurchaseRepository $purchaseRepository)
    {
        $this->purchaseRepository = $purchaseRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $purchases = $this->purchaseRepository->findBy($request->all());

        $purchaseResources = PurchaseResource::collection($purchases['purchases']);

        $purchaseResources->additional(Arr::except($purchases, ['purchases']));

        return $purchaseResources;
    }

    /**
     * @param IndexRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function purchaseReportPdf(IndexRequest $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $purchases = $this->purchaseRepository->findBy($request->all());

        return PdfHelper::downloadPdf($purchases['purchases'], 'pdf.reports.purchase', 'Purchase-report.pdf');
    }

    public function purchaseReportExcel(IndexRequest $request)
    {
        $purchases = $this->purchaseRepository->findBy($request->all());

        return Excel::download(new PurchaseReportExport($purchases['purchases']), 'Purchase-report.xlsx');
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function getPurchaseReturnProducts(IndexRequest $request): AnonymousResourceCollection
    {
        $purchases = $this->purchaseRepository->findByPurchaseReturnProducts($request->all());

        $purchaseResources = PurchaseProductReturnsGroupByDateResource::collection($purchases['returnPurchases']);

        unset($purchases['returnPurchases']);
        $purchaseResources->additional($purchases);

        return $purchaseResources;
    }

    public function getPurchaseReturnProductsPdf(IndexRequest $request)
    {
        $purchases = $this->purchaseRepository->findByPurchaseReturnProducts($request->all());

        return PdfHelper::downloadPdf($purchases['returnPurchases'], 'pdf.reports.purchaseReturn', 'Purchase-return-report.pdf');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return PurchaseResource
     */
    public function store(StoreRequest $request)
    {
        $purchase = $this->purchaseRepository->save($request->all());

        return new PurchaseResource($purchase);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Purchase $purchase
     * @return PurchaseResource
     */
    public function update(UpdateRequest $request, Purchase $purchase)
    {
        $product = $this->purchaseRepository->update($purchase, $request->all());

        return new PurchaseResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StatusUpdateRequest $request
     * @param Purchase $purchase
     * @return PurchaseResource
     */
    public function statusUpdate(StatusUpdateRequest $request, Purchase $purchase)
    {
        $product = $this->purchaseRepository->statusUpdate($purchase, $request->all());

        return new PurchaseResource($product);
    }

    /**
     * Display the specified resource.
     *
     * @param Purchase $purchase
     * @return PurchaseResource
     */
    public function show(Purchase $purchase)
    {
        return new PurchaseResource($purchase);
    }
}
