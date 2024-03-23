<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\IndexRequest as ProductListRequest;
use App\Http\Requests\Adjustment\IndexRequest as AdjustmentListRequest;
use App\Http\Requests\Purchase\IndexRequest as PurchaseListRequest;
use App\Http\Requests\Order\IndexRequest as OrderListRequest;
use App\Jobs\CreateAdjustmentExportFileJob;
use App\Jobs\CreateOrderExportFileJob;
use App\Jobs\CreateProductsExportFileJob;
use App\Jobs\CreatePurchaseExportFileJob;
use App\Jobs\CreateStockReportExportFileJob;
use App\Models\GenericExport;
use Illuminate\Http\JsonResponse;

class GenericExportController extends Controller
{
    /**
     * @var string
     */
    protected $message;

    /**
     *set default response message
     */
    public function __construct()
    {
        $this->message = 'We will sent you the generated file to your e-mail';
    }

    /**
     * @param array $searchCriteria
     * @return array
     */
    protected function setQueryParams(array $searchCriteria): array
    {
        $searchCriteria['per_page'] = 100;

        if (isset($searchCriteria['items']) && (int) $searchCriteria['items'] !== -1) {
            $searchCriteria['last_page'] = ceil((int) $searchCriteria['items'] / $searchCriteria['per_page']);
        } else {
            $searchCriteria['last_page'] = null;
        }

        return $searchCriteria;
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected function createGenericExport(array $data = [])
    {
        $fixedData = [
            'createdByUserId' => auth()->id(),
            'status' => 'pending',
            'statusMessage' => '',
            'fileName' => '',
        ];

        $genericData = array_merge($fixedData, $data);

        return GenericExport::create($genericData);
    }

    /**
     * @param AdjustmentListRequest $request
     * @param string $as
     * @return JsonResponse
     */
    public function adjustmentList(AdjustmentListRequest $request, string $as): JsonResponse
    {
        $searchCriteria = self::setQueryParams($request->all());

        $genericExport = self::createGenericExport([
            'viewPath' => 'export.adjustment-list',
            'exportAs' => $as,
            'items' => $searchCriteria['items'] ?? -1
        ]);

        dispatch(new CreateAdjustmentExportFileJob($genericExport, $searchCriteria));

        return response()->json(['message' => $this->message]);
    }

    /**
     * @param ProductListRequest $request
     * @param string $as
     * @return JsonResponse
     */
    public function productList(ProductListRequest $request, string $as): JsonResponse
    {
        $searchCriteria = self::setQueryParams($request->all());

        $genericExport = self::createGenericExport([
            'viewPath' => 'export.product-list',
            'exportAs' => $as,
            'items' => $searchCriteria['items'] ?? -1
        ]);

        dispatch(new CreateProductsExportFileJob($genericExport, $searchCriteria));

        return response()->json(['message' => $this->message]);
    }

    /**
     * @param PurchaseListRequest $request
     * @param string $as
     * @return JsonResponse
     */
    public function purchaseList(PurchaseListRequest $request, string $as): JsonResponse
    {
        $searchCriteria = self::setQueryParams($request->all());

        $genericExport = self::createGenericExport([
            'viewPath' => 'export.purchase-list',
            'exportAs' => $as,
            'items' => $searchCriteria['items'] ?? -1
        ]);

        dispatch(new CreatePurchaseExportFileJob($genericExport, $searchCriteria));

        return response()->json(['message' => $this->message]);
    }

    /**
     * @param OrderListRequest $request
     * @param string $as
     * @return JsonResponse
     */
    public function orderList(OrderListRequest $request, string $as): JsonResponse
    {
        $searchCriteria = self::setQueryParams($request->all());

        $genericExport = self::createGenericExport([
            'viewPath' => 'export.order-list',
            'exportAs' => $as,
            'items' => $searchCriteria['items'] ?? -1
        ]);

        dispatch(new CreateOrderExportFileJob($genericExport, $searchCriteria));

        return response()->json(['message' => $this->message]);
    }

    /**
     * @param ProductListRequest $request
     * @param string $as
     * @return JsonResponse
     */
    public function stockReport(ProductListRequest $request, string $as): JsonResponse
    {
        $searchCriteria = self::setQueryParams($request->all());

        $genericExport = self::createGenericExport([
            'viewPath' => 'export.stock-report',
            'exportAs' => $as,
            'items' => $searchCriteria['items'] ?? -1
        ]);

        dispatch(new CreateStockReportExportFileJob($genericExport, $searchCriteria));

        return response()->json(['message' => $this->message]);
    }
}
