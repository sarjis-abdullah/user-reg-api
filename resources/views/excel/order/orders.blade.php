<table>
    <tr>
        <th>Orders Report</th>
        <th>Print Date: {{ \Carbon\Carbon::now()->format('d M, Y, h:i:s A') }}</th>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th>SL</th>
            <th>Order number</th>
            <th>Customer name</th>
            <th>Customer phone number</th>
            <th>Customer address</th>
            <th>SKU</th>
            <th>Product name</th>
            <th>Qty</th>
            <th>Product price</th>
            <th>Vat</th>
            <th>Subtotal</th>
            <th>Delivery charge</th>
            <th>Order total amount</th>
            <th>Customer note</th>
        </tr>
    </thead>
    <tbody>
    @foreach(json_decode($orders) as $key => $order)
        @php
            $subTotal = 0;
            $vat = 0;
        @endphp
        @foreach($order->orderProducts as $orderProduct)
            @php
                $subTotal +=  $orderProduct->amount;
                $vat +=  $orderProduct->tax;
            @endphp
        @endforeach

        <tr>
            <td>{{ ($key+1) }}</td>
            <td>{{ $order->invoice }}</td>
            <td>{{ $order->customerName }}</td>
            <td>{{ $order->customer->phone }}</td>
            <td>{{ $order->customer->address }}</td>
            <td>
                @foreach($order->orderProducts as $sKey => $orderProduct)
                {{ $orderProduct->stock->sku }}
                @if(count($order->orderProducts) !== ($sKey+1))
                        <br>
                @endif
                @endforeach
            </td>
            <td>
                @foreach($order->orderProducts as $sKey => $orderProduct)
                    {{ $orderProduct->product->name }}
                    @if(count($order->orderProducts) !== ($sKey+1))
                        <br>
                    @endif
                @endforeach
            </td>
            <td>
                @foreach($order->orderProducts as $sKey => $orderProduct)
                    {{ $orderProduct->quantity }}
                    @if(count($order->orderProducts) !== ($sKey+1))
                        <br>
                    @endif
                @endforeach
            </td>
            <td>
                @foreach($order->orderProducts as $sKey => $orderProduct)
                    {{ $orderProduct->unitPrice }}
                    @if(count($order->orderProducts) !== ($sKey+1))
                        <br>
                    @endif
                @endforeach
            </td>
            <td>{{ $vat }}</td>
            <td>{{ $subTotal }}</td>
            <td>{{ $order->shippingCost }}</td>
            <td>{{ $order->amount }}</td>
            <td>{{ $order->comment }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
