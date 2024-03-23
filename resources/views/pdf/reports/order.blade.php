@extends('pdf.layout')
@section('title', 'Sale Report')
@section('pdfContent')
    @if((request()->filled('startDate') && request()->filled('endDate')) || request()->filled('paymentStatusGroup') || request()->filled('customerId'))
    <table style="margin-bottom: 10px">
        <tr>
            <th>
                @if(request()->filled('startDate') && request()->filled('endDate'))
                    Date: {{ date('d M, Y', strtotime(request()->get('startDate'))) .' to '. date('d M, Y', strtotime(request()->get('endDate'))) }}
                @endif
                @if(request()->filled('paymentStatusGroup'))
                    &nbsp;&nbsp;&nbsp; Payment Status: {{ ucfirst(request()->get('paymentStatusGroup')) }}
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
                <th>RETURN AMOUNT</th>
                <th>NET SALES AMOUNT</th>
                <th>PAID AMOUNT</th>
                <th>DUE</th>
                <th>DISCOUNT</th>
                <th>GROSS PROFIT</th>
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
                $total_gross_profit_amount = 0;
            @endphp
            @foreach($data as $key => $order)
                @php
                    $amount = round($order->amount, 2);
                    $return_amount = round($order->orderProductReturns->sum('returnAmount'), 2);
                    $net_paid_amount = round($order->paid - $order->orderProductReturns->sum('returnAmount'), 2);
                    $net_sale_amount = round($order->paid, 2);
                    $due_amount = round($order->due, 2);
                    $discount_amount = round($order->discount, 2);
                    $gross_profit_amount = round($order->grossProfit - ($order->orderProductReturns->sum('profitAmount') - $order->orderProductReturns->sum('discountAmount')), 2);
                @endphp
                <tr>
                    <td>{{ ($key+1) }}</td>
                    <td>
                        {{ date('d-m-Y', strtotime($order->date)) }}
                        <br>
                        <small>{{ date('h:i:s A', strtotime($order->created_at)) }}</small>
                    </td>
                    <td>{{ $order->invoice }}</td>
                    <td>{{ $order->customer->name }}</td>
                    <td>{{ $amount }}</td>
                    <td>{{ $return_amount }}</td>
                    <td>{{ $net_paid_amount }}</td>
                    <td>{{ $net_sale_amount }}</td>
                    <td>{{ $due_amount }}</td>
                    <td>{{ $discount_amount }}</td>
                    <td>{{ $gross_profit_amount }}</td>
                </tr>
                @php
                    $total_amount += $amount;
                    $total_return_amount += $return_amount;
                    $total_net_paid_amount += $net_paid_amount;
                    $total_net_sale_amount += $net_sale_amount;
                    $total_due_amount += $due_amount;
                    $total_discount_amount += $discount_amount;
                    $total_gross_profit_amount += $gross_profit_amount;
                @endphp
            @endforeach
            <tr>
                <th>Total:</th>
                <th></th>
                <th></th>
                <th></th>
                <th>{{ $total_amount }}</th>
                <th>{{ $total_return_amount }}</th>
                <th>{{ $total_net_paid_amount }}</th>
                <th>{{ $total_net_sale_amount }}</th>
                <th>{{ $total_due_amount }}</th>
                <th>{{ $total_discount_amount }}</th>
                <th>{{ $total_gross_profit_amount }}</th>
            </tr>
            </tbody>
        </table>
@endsection
