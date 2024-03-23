<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use App\Exports\SalesPersonWiseOrderExport;
use App\Http\Requests\Order\ChangeOrderStatusRequest;
use App\Http\Requests\Order\ChangeStatusRequest;
use App\Http\Requests\Order\ExchangeRequest;
use App\Http\Requests\Order\IndexRequest;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Resources\OrderProductReturnsGroupByDateResource;
use App\Http\Resources\OrderResource;
use App\Models\AppSetting;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepository;
use App\Services\Helpers\PdfHelper;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * ProductController constructor.
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $orders = $this->orderRepository->findBy($request->all());

        $orderResources = OrderResource::collection($orders['orders']);

        $orderResources->additional(Arr::except($orders, ['orders']));

        return $orderResources;
    }


    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function getOrderReturnProducts(IndexRequest $request): AnonymousResourceCollection
    {
        $returnOrders = $this->orderRepository->findByOrderReturnProducts($request->all());

        $returnOrderResources =  OrderProductReturnsGroupByDateResource::collection($returnOrders['returnOrders']);

        unset($returnOrders['returnOrders']);

        $returnOrderResources->additional($returnOrders);

        return $returnOrderResources;
    }

    /**
     * @param IndexRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function getOrderReturnProductsPdf(IndexRequest $request): StreamedResponse
    {
        $returnOrders = $this->orderRepository->findByOrderReturnProducts($request->all());

        return PdfHelper::downloadPdf($returnOrders['returnOrders'], 'pdf.reports.saleReturn', 'Sale-return.pdf');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return OrderResource
     */
    public function store(StoreRequest $request)
    {
        $order = $this->orderRepository->save($request->all());

        return new OrderResource($order);
    }

    /**
     * Display the specified resource.
     *
     * @param Order $order
     * @return OrderResource
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }


    public function orderReportPdf(IndexRequest $request)
    {
        $orders = $this->orderRepository->findBy($request->all());

        $orderResources =  OrderResource::collection(($orders['orders']));

        $orderResources->additional(Arr::except($orders, ['orders']));

        return PdfHelper::downloadPdf($orderResources, 'pdf.reports.order', 'Order-report');
    }

    /**
     * @param Order $order
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function orderInvoicePdf(Order $order): StreamedResponse
    {
        $orderDetails =  new OrderResource($order);

        $invoiceSetting = AppSetting::query()
            ->where('type', AppSetting::TYPE_INVOICE)
            ->where('branchId', $order->branchId)
            ->first();

        $data = [
            'order_data' => $orderDetails,
            'invoice_setting' => $invoiceSetting->settings
        ];

        return PdfHelper::downloadPdf(json_encode($data), 'pdf.order.orderInvoice', 'Order-invoice.pdf');
    }

    public function previewOrderInvoicePdf($id)
    {
        return view('pdf.order.previewOrderInvoice',['order' => $id]);
    }

    /**
     * @param IndexRequest $request
     * @return BinaryFileResponse
     */
    public function orderExcelExport(IndexRequest $request): BinaryFileResponse
    {
        $orders = $this->orderRepository->findBy($request->all());

        $orderResources = OrderResource::collection($orders['orders']);

        return Excel::download(new OrderExport($orderResources), 'Orders.xlsx');
    }

    /**
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function salesPersonOrder(IndexRequest $request): AnonymousResourceCollection
    {
        $orders = $this->orderRepository->salesManWiseOrder($request->all());

        $orderResources = OrderResource::collection($orders['orders']);

        return $orderResources->additional(['pageWiseSummary' => $orders['pageWiseSummary'], 'summary' => $orders['summary']]);
    }

    /**
     * @param IndexRequest $request
     * @return StreamedResponse
     * @throws MpdfException
     */
    public function salesPersonOrderPDF(IndexRequest $request): StreamedResponse
    {
        $orders = $this->orderRepository->salesManWiseOrder($request->all());

        $orderResources = OrderResource::collection($orders['orders']);

        return PdfHelper::downloadPdf($orderResources, 'pdf.reports.salesPersonOrder', 'Sales-person-order.pdf');
    }

    public function salesPersonOrderExcel(IndexRequest $request)
    {
        $orders = $this->orderRepository->salesManWiseOrder($request->all());

        $orderResources = OrderResource::collection($orders['orders']);

        return Excel::download(new SalesPersonWiseOrderExport($orderResources), 'Sales-person-wise-order.xlsx');
    }

    /**
     * @param ChangeStatusRequest $request
     * @param $orderId
     * @return OrderResource
     */
    public function changeStatus(ChangeStatusRequest $request, $orderId): OrderResource
    {
        $order = $this->orderRepository->changeStatus($request->all(), $orderId);

        return new OrderResource($order);
    }

    /**
     * @param ChangeOrderStatusRequest $request
     * @param $orderId
     * @return OrderResource
     */
    public function changeOrderStatus(ChangeOrderStatusRequest $request, $orderId): OrderResource
    {
        $order = $this->orderRepository->changeOrderStatus($request->all(), $orderId);

        return new OrderResource($order);
    }


    /**
     * @param ExchangeRequest $request
     * @return OrderResource
     */
    public function orderExchange(ExchangeRequest $request): OrderResource
    {
        $order = $this->orderRepository->saveExchange($request->all());

        return new OrderResource($order);
    }

}
