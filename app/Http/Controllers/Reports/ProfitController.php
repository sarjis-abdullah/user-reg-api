<?php

namespace App\Http\Controllers\Reports;

use App\Exports\CashierReportExport;
use App\Exports\CategoryWiseSaleExport;
use App\Exports\DateWiseSaleExport;
use App\Exports\ProductWiseSaleExport;
use App\Exports\SalesPersonReportExport;
use App\Exports\SalesWiseVatExport;
use App\Exports\SupplierWisePurchaseExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\CashierReportRequest;
use App\Http\Requests\Reports\CategoryWiseSaleRequest;
use App\Http\Requests\Reports\DateWiseProfitRequest;
use App\Http\Requests\Reports\ProductWiseProfitRequest;
use App\Http\Requests\Reports\ProductWiseVatRequest;
use App\Http\Requests\Reports\SalesPersonReportRequest;
use App\Http\Requests\Reports\SalesWiseVatRequest;
use App\Http\Requests\Reports\SupplierWisePurchaseRequest;
use App\Http\Requests\Reports\SupplierWiseStockRequest;
use App\Http\Resources\Reports\CashierResource;
use App\Http\Resources\Reports\CategoryWiseSaleResourceCollection;
use App\Http\Resources\Reports\DateWiseProfitResourceCollection;
use App\Http\Resources\Reports\ProductWiseProfitResourceCollection;
use App\Http\Resources\Reports\ProductWiseVatResource;
use App\Http\Resources\Reports\SalesPersonResource;
use App\Http\Resources\Reports\SalesWiseVatResource;
use App\Http\Resources\Reports\SupplierWisePurchaseResourceCollection;
use App\Http\Resources\Reports\SupplierWiseStockResourceCollection;
use App\Services\Helpers\PdfHelper;
use App\Services\Reports\Profit;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfitController extends Controller
{
    /**
     * @param DateWiseProfitRequest $request
     * @return DateWiseProfitResourceCollection
     */
    public function getDateWiseSaleProfit(DateWiseProfitRequest $request): DateWiseProfitResourceCollection
    {
        $data = Profit::getDateWiseSaleProfit($request->all());

        $resources =  new DateWiseProfitResourceCollection($data['result']);

        $resources->additional(['summary' => $data['summary'], 'pageWiseSummary' => $data['pageWiseSummary']]);

        return $resources;
    }

    /**
     * Export Date wise sale report.
     *
     * @param DateWiseProfitRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function getDateWiseSaleProfitPdf(DateWiseProfitRequest $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $data = Profit::getDateWiseSaleProfit($request->all());

        return PdfHelper::downloadPdf($data['result'],'pdf.reports.dateWiseSale', 'Date-wise-sale-report.pdf');
    }

    /**
     * @param DateWiseProfitRequest $request
     * @return BinaryFileResponse
     */
    public function getDateWiseSaleProfitExcel(DateWiseProfitRequest $request): BinaryFileResponse
    {
        $data = Profit::getDateWiseSaleProfit($request->all());

        return Excel::download(new DateWiseSaleExport($data['result']), 'Date-wise-sale-report.xlsx');
    }

    /**
     * @param ProductWiseProfitRequest $request
     * @return ProductWiseProfitResourceCollection
     */
    public function getProductWiseSaleProfit(ProductWiseProfitRequest $request): ProductWiseProfitResourceCollection
    {
        $data = Profit::getProductWiseSaleProfit($request->all());

        $resources =  new ProductWiseProfitResourceCollection($data['result']);

        $resources->additional(['summary' => $data['summary'], 'pageWiseSummary' => $data['pageWiseSummary']]);

        return $resources;
    }

    /**
     * Export product wise sale report.
     *
     * @param ProductWiseProfitRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function getProductWiseSaleProfitPdf(ProductWiseProfitRequest $request): StreamedResponse
    {
        $data = Profit::getProductWiseSaleProfit($request->all());

        $resources =  new ProductWiseProfitResourceCollection($data['result']);

        return PdfHelper::downloadPdf(json_encode($resources), 'pdf.reports.productWiseSale', 'Product-wise-sale-report.pdf');
    }

    /**
     * @param ProductWiseProfitRequest $request
     * @return BinaryFileResponse
     */
    public function getProductWiseSaleProfitExcel(ProductWiseProfitRequest $request): BinaryFileResponse
    {
        $data = Profit::getProductWiseSaleProfit($request->all());

        $resources =  new ProductWiseProfitResourceCollection($data['result']);

        return Excel::download(new ProductWiseSaleExport($resources), 'Product-wise-sale.xlsx');
    }

    /**
     * Export Category wise sale report.
     *
     * @param CategoryWiseSaleRequest $request
     * @return CategoryWiseSaleResourceCollection
     */
    public function getCategoryWiseSaleReport(CategoryWiseSaleRequest $request): CategoryWiseSaleResourceCollection
    {
        $data = Profit::getCategoryWiseSaleReport($request->all());

        $resources =  new CategoryWiseSaleResourceCollection($data['result']);

        $resources->additional(['summary' => $data['summary'], 'pageWiseSummary' => $data['pageWiseSummary']]);

        return $resources;
    }

    /**
     * Category wise sale report.
     *
     * @param CategoryWiseSaleRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function getCategoryWiseSaleReportPdf(CategoryWiseSaleRequest $request): StreamedResponse
    {
        $data = Profit::getCategoryWiseSaleReport($request->all());

        $resources =  new CategoryWiseSaleResourceCollection($data['result']);

        return PdfHelper::downloadPdf(json_encode($resources), 'pdf.reports.categoryWiseSale', 'Category-wise-sale-report.pdf');
    }

    /**
     * @param CategoryWiseSaleRequest $request
     * @return BinaryFileResponse
     */
    public function getCategoryWiseSaleReportExcel(CategoryWiseSaleRequest $request): BinaryFileResponse
    {
        $data = Profit::getCategoryWiseSaleReport($request->all());

        $resources =  new CategoryWiseSaleResourceCollection($data['result']);

        return Excel::download(new CategoryWiseSaleExport($resources), 'Category-wise-sale.xlsx');
    }


    /**
     * @param SupplierWisePurchaseRequest $request
     * @return SupplierWisePurchaseResourceCollection
     */
    public function getSupplierWisePurchaseReport(SupplierWisePurchaseRequest $request): SupplierWisePurchaseResourceCollection
    {
        $data = Profit::getSupplierWisePurchaseReport($request->all());

        return  new SupplierWisePurchaseResourceCollection($data['result']);
    }

    /**
     * @param SupplierWisePurchaseRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function getSupplierWisePurchaseReportPdf(SupplierWisePurchaseRequest $request): StreamedResponse
    {
        $data = Profit::getSupplierWisePurchaseReport($request->all());

        return PdfHelper::downloadPdf($data['result'], 'pdf.reports.supplierWisePurchase', 'Supplier-wise-purchase-report.pdf');
    }

    /**
     * @param SupplierWisePurchaseRequest $request
     * @return BinaryFileResponse
     */
    public function getSupplierWisePurchaseReportExcel(SupplierWisePurchaseRequest $request): BinaryFileResponse
    {
        $data = Profit::getSupplierWisePurchaseReport($request->all());

        return Excel::download(new SupplierWisePurchaseExport($data['result']), 'Supplier-wise-purchase.xlsx');
    }

    /**
     * @param SupplierWiseStockRequest $request
     * @return SupplierWiseStockResourceCollection
     */
    public function getSupplierWiseStockReport(SupplierWiseStockRequest $request): SupplierWiseStockResourceCollection
    {
        $data = Profit::getSupplierWiseStockReport($request->all());

        $resources =  new SupplierWiseStockResourceCollection($data['result']);

        return $resources;
    }

    /**
     * @param SupplierWiseStockRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function getSupplierWiseStockReportPdf(SupplierWiseStockRequest $request): StreamedResponse
    {
        $data = Profit::getSupplierWiseStockReport($request->all());

        return PdfHelper::downloadPdf($data['result'], 'pdf.reports.supplierWiseStock', 'Supplier-wise-stock.pdf');
    }

    /**
     * Sale wise vat report
     *
     * @param SalesWiseVatRequest $request
     * @return AnonymousResourceCollection
     */
    public function salesWiseVatReport(SalesWiseVatRequest $request): AnonymousResourceCollection
    {
        $data = Profit::getSalesWiseVatReport($request);

        $resources = SalesWiseVatResource::collection($data['result']);

        return $resources->additional(['summary' => $data['summary'], 'pageWiseSummary' => $data['pageWiseSummary']]);
    }

    /**
     * Product wise sale report
     *
     * @param ProductWiseVatRequest $request
     * @return AnonymousResourceCollection
     */
    public function productWiseVatReport(ProductWiseVatRequest $request): AnonymousResourceCollection
    {
        $data = Profit::getProductWiseVatReport($request);

        $resources = ProductWiseVatResource::collection($data['result']);

        return $resources->additional(['summary' => $data['summary'], 'pageWiseSummary' => $data['pageWiseSummary']]);
    }

    /**
     * @param SalesWiseVatRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function salesWiseVatReportPdf(SalesWiseVatRequest $request): StreamedResponse
    {
        $data = Profit::getSalesWiseVatReport($request);

        $resources = SalesWiseVatResource::collection($data['result']);

        return PdfHelper::downloadPdf(json_encode($resources), 'pdf.reports.salesWiseVatReport', 'Sales-wise-vat-report.pdf');
    }

    /**
     * @param SalesWiseVatRequest $request
     * @return BinaryFileResponse
     */
    public function salesWiseVatReportExcel(SalesWiseVatRequest $request): BinaryFileResponse
    {
        $data = Profit::getSalesWiseVatReport($request);

        $resources = SalesWiseVatResource::collection($data['result']);

        return Excel::download(new SalesWiseVatExport($resources), 'Sales-wise-vat.xlsx');
    }


    /**
     * @param SalesPersonReportRequest $request
     * @return AnonymousResourceCollection
     */
    public function salesPersonReport(SalesPersonReportRequest $request): AnonymousResourceCollection
    {
        $data = Profit::getSalesPersonReport($request);

        $resources = SalesPersonResource::collection($data['result']);

        return $resources->additional(['summary' => $data['summary'], 'pageWiseSummary' => $data['pageWiseSummary']]);
    }

    /**
     * @param SalesPersonReportRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function salesPersonReportPdf(SalesPersonReportRequest $request): StreamedResponse
    {
        $data = Profit::getSalesPersonReport($request);

        $resources = SalesPersonResource::collection($data['result']);

        return PdfHelper::downloadPdf(json_encode($resources), 'pdf.reports.salesPersonReport', 'Sales-person-report.pdf');
    }

    /**
     * @param SalesPersonReportRequest $request
     * @return BinaryFileResponse
     */
    public function salesPersonReportExcel(SalesPersonReportRequest $request): BinaryFileResponse
    {
        $data = Profit::getSalesPersonReport($request);

        $resources = SalesPersonResource::collection($data['result']);

        return Excel::download(new SalesPersonReportExport(json_encode($resources)), 'Sales-person-report.xlsx');
    }



    /**
     * @param CashierReportRequest $request
     * @return AnonymousResourceCollection
     */
    public function cashierReport(CashierReportRequest $request): AnonymousResourceCollection
    {
        $data = Profit::getCashierReport($request);

        $resources = CashierResource::collection($data['result']);

        return $resources->additional(['summary' => $data['summary'], 'pageWiseSummary' => $data['pageWiseSummary']]);
    }

    /**
     * @param CashierReportRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function cashierReportPdf(CashierReportRequest $request): StreamedResponse
    {
        $data = Profit::getCashierReport($request);

        $resources = CashierResource::collection($data['result']);

        return PdfHelper::downloadPdf(json_encode($resources), 'pdf.reports.cashierReport', 'Cashier-report.pdf');
    }

    /**
     * @param CashierReportRequest $request
     * @return BinaryFileResponse
     */
    public function cashierReportExcel(CashierReportRequest $request): BinaryFileResponse
    {
        $data = Profit::getCashierReport($request);

        $resources = CashierResource::collection($data['result']);

        return Excel::download(new CashierReportExport(json_encode($resources)), 'Cashier-report.xlsx');
    }
}
