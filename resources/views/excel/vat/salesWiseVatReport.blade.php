<table>
    <tr>
        <th>Sales Wise Vat Report</th>
        <th>Print Date: {{ \Carbon\Carbon::now()->format('d M, Y, h:i:s A') }}</th>
    </tr>
</table>
@if((request()->filled('startDate') && request()->filled('endDate')) || request()->filled('customerId'))
    <table style="margin-bottom: 10px">
        <tr>
            <th>
                @if(request()->filled('startDate') && request()->filled('endDate'))
                    Date: {{ date('d M, Y', strtotime(request()->get('startDate'))) .' to '. date('d M, Y', strtotime(request()->get('endDate'))) }}
                @endif
                @if(request()->filled('customerId'))
                    @php
                        $customer = \App\Models\Customer::query()->where('id', request()->get('customerId'))->first(['name']);
                    @endphp
                    &nbsp;&nbsp;&nbsp; Customer: {{ ucfirst($customer->name) }}
                @endif
            </th>
        </tr>
    </table>
@endif
<table>
    <thead>
    <tr>
        <th>SL NO</th>
        <th>DATE</th>
        <th>INVOICE</th>
        <th>CUSTOMER</th>
        <th>GRAND TOTAL</th>
        <th>DUE</th>
        <th>DISCOUNT</th>
        <th>GROSS PROFIT</th>
        <th>VAT</th>
    </tr>
    </thead>
    <tbody>
    @php
        $total_amount = 0;
        $total_return_amount = 0;
        $total_net_paid_amount = 0;
        $total_net_sale_amount = 0;
        $total_due_amount = 0;
        $total_discount_amount = 0;
        $total_tax_amount = 0;
    @endphp
    @foreach(json_decode($orders) as $key => $order)
        @php
            $amount = round($order->amount, 2);
            $net_sale_amount = round($order->paid, 2);
            $due_amount = round($order->due, 2);
            $discount_amount = round($order->discount, 2);
            $taxAmount = $order->tax;
        @endphp
        <tr>
            <td>{{ ($key+1) }}</td>
            <td>
                {{ date('d-m-Y', strtotime($order->date)) }}
                <br>
                <small>{{ date('h:i:s A', strtotime($order->created_at)) }}</small>
            </td>
            <td>{{ $order->invoice }}</td>
            <td>{{ $order->customerName }}</td>
            <td>{{ $amount }}</td>
            <td>{{ $net_sale_amount }}</td>
            <td>{{ $due_amount }}</td>
            <td>{{ $discount_amount }}</td>
            <td>{{ $taxAmount }}</td>
        </tr>
        @php
            $total_amount += $amount;
            $total_net_sale_amount += $net_sale_amount;
            $total_due_amount += $due_amount;
            $total_discount_amount += $discount_amount;
            $total_tax_amount += $taxAmount;
        @endphp
    @endforeach
    <tr>
        <th>Total:</th>
        <th></th>
        <th></th>
        <th></th>
        <th>{{ $total_amount }}</th>
        <th>{{ $total_net_sale_amount }}</th>
        <th>{{ $total_due_amount }}</th>
        <th>{{ $total_discount_amount }}</th>
        <th>{{ $total_tax_amount }}</th>
    </tr>
    </tbody>
</table>
