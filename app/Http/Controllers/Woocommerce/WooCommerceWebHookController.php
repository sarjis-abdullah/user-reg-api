<?php

namespace App\Http\Controllers\Woocommerce;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Payment;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\PaymentRepository;
use App\Repositories\Contracts\StockRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WooCommerceWebHookController extends Controller
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;
    /**
     * @var StockRepository
     */
    protected $stockRepository;

    public function __construct(OrderRepository $orderRepository, CustomerRepository $customerRepository, StockRepository $stockRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->stockRepository = $stockRepository;
    }

    public function store(Request $request): JsonResponse
    {
        # Verify the secret key
        $signature = $request->header('X-WC-Webhook-Signature');
        if (empty($signature)) {
            return response()->json([ 'error' => 'Invalid secret key'], 401);
        }

        $consumerKey = 'this-is-rt-secret';

        $payload = $request->getContent();
        $calculatedHmac = base64_encode(hash_hmac('sha256', $payload, $consumerKey, true));

        if ($signature != $calculatedHmac) {
            // Invalid secret key, log and respond accordingly
            Log::error('Invalid secret key received in webhook request.');
            return response()->json(['error' => 'Invalid secret key received in webhook request'], 401);
        }

        // Handle the order.created event
        $data = $request->all();

        if($data) {
            $branch = Branch::where('type', Branch::TYPE_ECOMMERCE)->latest()->first();
            if (!$branch instanceof Branch) {
                return response()->json(['message' => 'No Branch found to accept the order!'], 200);
            }

            $customer = self::createOrGetCustomerByPhoneOrEmail($data['billing']);

            $orderProducts = collect($data['line_items'])
                ->reject(function ($item) use ($branch) {
                    $stock = $this->stockRepository->findOneBy(['branchId' => $branch->id, 'sku' => $item['sku']]);
                    return empty($stock);
                })
                ->map(function ($item) use ($branch) {
                    $stock = $this->stockRepository->findOneBy(['branchId' => $branch->id, 'sku' => $item['sku']]);
                    return [
                        "productId" => $stock->productId,
                        "stockId" => $stock->id,
                        "quantity" => (float) $item["quantity"],
                        "unitPrice" => $item['price'],
                        "discountedUnitPrice" => (float) $item['total'] / $item['quantity'],
                        "amount" => (float) $item["total"] + (float) $item['total_tax'],
                        "discount" => ($item["price"] - (float) $item['total'] / $item['quantity']) * $item['quantity'],
                        "tax" => $item["total_tax"],
                        "taxId" => $stock->product && $stock->product->taxId ? $stock->product->taxId : null
                    ];
                })->values()->all();

            if(count($orderProducts)) {
                $orderData = [
                    "branchId" => $branch->id,
                    "customerId" => $customer->id,
                    "ecomInvoice" => $data['order_key'],
                    "shipping" => $data['shipping'],
                    'orderUrl' => $data['_links']['self'] ? $data['_links']['self'][0]['href'] : null,
                    "referenceId" => $data['id'],
                    "terminal" => "online",
                    "amount" => (float) $data['total'],
                    "discount" => (float) $data['discount_total'],
                    "shippingCost" => (float) $data['shipping_total'],
                    "tax" => (float) $data['cart_tax'],
                    "deliveryMethod" => "online",
                    "date" => $data['date_created'],
                    "comment" => $data['customer_note'],
                    'status' => $data['status'],
                    'createdByUserId' => 1,
                    'orderProducts' => $orderProducts
                ];

                if(in_array($data['status'], [Order::STATUS_PROCESSING, Order::STATUS_COMPLETED, Order::STATUS_SHIPPED])) {
                    if(isset($data['payment_method']) && $data['total'] > 0) {
                        $orderData["payment"] = [
                            "amount" => $data['total'],
                            "method" => $data['payment_method'],
                            "txnNumber" => $data['transaction_id'],
                            "referenceNumber" => $data['order_key'],
                            "createdByUserId" => 1
                        ];
                    }
                }

                $this->orderRepository->save($orderData);
            }
        }

        return response()->json(['message' => 'Webhook received successfully'], 200);
    }

    public function update(Request $request): JsonResponse
    {
        # Verify the secret key
        $signature = $request->header('X-WC-Webhook-Signature');
        if (empty($signature)) {
            return response()->json([ 'error' => 'Invalid secret key'], 401);
        }

        $consumerKey = 'this-is-rt-secret';

        $payload = $request->getContent();
        $calculatedHmac = base64_encode(hash_hmac('sha256', $payload, $consumerKey, true));

        if ($signature != $calculatedHmac) {
            // Invalid secret key, log and respond accordingly
            Log::error('Invalid secret key received in webhook request.');
            return response()->json(['error' => 'Invalid secret key'], 401);
        }

        // Handle the order.created event
        $data = $request->all();

        if($data) {
            $orderData = [
                "shipping" => $data['shipping'],
                "comment" => $data['customer_note'],
                'status' => $data['status']
            ];

            $order = $this->orderRepository->findOneBy(['ecomInvoice' => $data['order_key']]);

            if($order instanceof Order) {
                if($order->status !== $data['status']) {
                    if(isset($data['payment_method']) && $data['total'] > 0) {
                        $paymentData['status'] = Payment::STATUS_SUCCESS;
                        $paymentData['cashFlow'] = Payment::CASH_FLOW_IN;
                        $paymentData['paymentableId'] = $order->id;
                        $paymentData['paymentableType'] = Payment::PAYMENT_SOURCE_ORDER;
                        $paymentData['payType'] = Payment::PAY_TYPE_ORDER;
                        $paymentData['receivedByUserId'] = $order->createdByUserId;

                        $paymentRepository = app(PaymentRepository::class);
                        $payment = $paymentRepository->save($paymentData);

                        $orderData['paid'] = $order->paid + $payment->amount;
                        $orderData['due'] = round($order->amount, 2) - round($orderData['paid'],2);
                        $orderData['date'] =Carbon::now();
                        $orderData['paymentStatus'] = Payment::paymentStatus($orderData['due'], $orderData['paid']);
                    }
                }

                $this->orderRepository->update($order, $orderData);
            } else {
                return response()->json(['message' => 'No data found'], 204);
            }

        }

        return response()->json(['message' => 'Webhook received successfully'], 200);
    }

    /**
     * create or get company by name
     *
     * @param $customerData
     * @return \ArrayAccess|null
     */
    protected function createOrGetCustomerByPhoneOrEmail($customerData): ?\ArrayAccess
    {
        $customer = $this->customerRepository->getModel()
            ->when($customerData['phone'], function ($query) use($customerData) {
                $query->where('phone', $customerData['phone']);
            })
            ->when($customerData['email'], function ($query) use($customerData) {
                $query->where('email', $customerData['email']);
            })
            ->first();

        if (!$customer) {
            $customer = $this->customerRepository->save(
                [
                    'name' => $customerData['first_name'] . ' ' . $customerData['last_name'],
                    'email' => $customerData['email'],
                    'phone' => $customerData['phone'],
                    'address' => $customerData['address_1'],
                    'address2' => $customerData['address_2'],
                    'city' => $customerData['city'],
                    'state' => $customerData['state'],
                    'postCode' => $customerData['postcode']
                ]
            );
        }

        return $customer;
    }
}
