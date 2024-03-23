<?php

namespace App\Http\Controllers;

use App\Exports\PaymentSummaryExport;
use App\Http\Requests\Payment\IndexRequest;
use App\Http\Requests\Payment\PaymentSummaryRequest;
use App\Http\Requests\Payment\StoreRequest;
use App\Http\Requests\Payment\UpdateRequest;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\PaymentSummaryResource;
use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepository;
use App\Services\Helpers\PdfHelper;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @param PaymentRepository $paymentRepository
     */
    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $payments = $this->paymentRepository->findBy($request->all());

        return PaymentResource::collection($payments);
    }

    /**
     * Display a listing of the resource.
     *
     * @param StoreRequest $request
     * @return PaymentResource
     */
    public function store(StoreRequest $request): PaymentResource
    {
        $payment = $this->paymentRepository->save($request->all());

        return new PaymentResource($payment);
    }

    /**
     * Display the specified resource.
     *
     * @param Payment $payment
     * @return PaymentResource
     */
    public function show(Payment $payment): PaymentResource
    {
        return new PaymentResource($payment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Payment $payment
     * @return PaymentResource
     */
    public function update(UpdateRequest $request, Payment $payment): PaymentResource
    {
        $payment = $this->paymentRepository->update($payment, $request->all());

        return new PaymentResource($payment);
    }

    /**
     * @param PaymentSummaryRequest $request
     * @return AnonymousResourceCollection
     */
    public function paymentSummary(PaymentSummaryRequest $request)
    {
        $data = $this->paymentRepository->paymentSummary($request->all());

        $resources = PaymentSummaryResource::collection($data['result']);

        $resources->additional(['summary' => $data['summary']]);

        return $resources;

    }

    /**
     * @param PaymentSummaryRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function paymentSummaryPdf(PaymentSummaryRequest $request): StreamedResponse
    {
        $data = $this->paymentRepository->paymentSummary($request->all());

        return PdfHelper::downloadPdf($data['result'], 'pdf.reports.paymentSummaryReport', 'Payment-summary-report');
    }

    /**
     * @param PaymentSummaryRequest $request
     * @return BinaryFileResponse
     */
    public function paymentSummaryExcel(PaymentSummaryRequest $request): BinaryFileResponse
    {
        $data = $this->paymentRepository->paymentSummary($request->all());

        return Excel::download(new PaymentSummaryExport($data['result']), 'Payment-summary.xlsx');
    }
}
