@extends('pdf.layout')
@section('title', 'Sales person wise order report')
@section('pdfContent')
    @if((request()->filled('startDate') && request()->filled('endDate')) || request()->filled('paymentStatusGroup') || request()->filled('salePersonId'))
    <table style="margin-bottom: 10px">
        <tr>
            <th>
                @if(request()->filled('startDate') && request()->filled('endDate'))
                    Date: {{ date('d M, Y', strtotime(request()->get('startDate'))) .' to '. date('d M, Y', strtotime(request()->get('endDate'))) }}
                @endif
                @if(request()->filled('paymentStatusGroup'))
                    &nbsp;&nbsp;&nbsp; Payment Status: {{ ucfirst(request()->get('paymentStatusGroup')) }}
                @endif
                @if(request()->filled('salePersonId'))
                    @php
                        $salesPerson = \App\Models\Employee::query()->where('id', request()->get('salePersonId'))->first(['title']);
                    @endphp
                        &nbsp;&nbsp;&nbsSales person: {{ ucfirst($salesPerson->title) }}
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
            <th>SALES PERSON</th>
            <th>INVOICE</th>
            <th>Sale Amount</th>
            <th>RETURN AMOUNT</th>
            <th>NET SALES AMOUNT</th>
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
                $net_sale_amount = (round($order->amount, 2) - round($order->orderProductReturns->sum('returnAmount'), 2));
            @endphp
            <tr>
                <td>{{ ($key+1) }}</td>
                <td>
                    {{ date('d-m-Y', strtotime($order->date)) }}
                    <br>
                    <small>{{ date('h:i:s A', strtotime($order->created_at)) }}</small>
                </td>
                <td>{{ $order->salePerson->user->name }}</td>
                <td>{{ $order->invoice }}</td>
                <td>{{ $amount }}</td>
                <td>{{ $return_amount }}</td>
                <td>{{ $net_sale_amount }}</td>
            </tr>
            @php
                $total_amount += $amount;
                $total_return_amount += $return_amount;
                $total_net_sale_amount += $net_sale_amount;
            @endphp
        @endforeach
        <tr>
            <th>Total:</th>
            <th></th>
            <th></th>
            <th></th>
            <th>{{ $total_amount }}</th>
            <th>{{ $total_return_amount }}</th>
            <th>{{ $total_net_sale_amount }}</th>
        </tr>
        </tbody>
    </table>
@endsection
