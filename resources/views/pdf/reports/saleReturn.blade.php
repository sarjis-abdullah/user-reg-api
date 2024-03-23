@extends('pdf.layout')
@section('title', 'Sale Return Report')
@section('pdfContent')
    @if((request()->filled('orderReturnStartDate') && request()->filled('orderReturnEndDate')) || request()->filled('customerId'))
    <table style="margin-bottom: 10px">
        <tr>
            <th>
                @if(request()->filled('orderReturnStartDate') && request()->filled('orderReturnEndDate'))
                    Date: {{ date('d M, Y', strtotime(request()->get('orderReturnStartDate'))) .' to '. date('d M, Y', strtotime(request()->get('orderReturnEndDate'))) }}
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
                <th>INVOICE</th>
                <th>BRANCH</th>
                <th>CUSTOMER</th>
                <th>RETURN DATE</th>
                <th>PRODUCT</th>
                <th>QUANTITY</th>
                <th>RETURN AMOUNT</th>
                <th>RETURN BY</th>
            </tr>
            </thead>
            <tbody>
            @php
                $totalQuantity = 0;
                $totalReturnAmount = 0;
            @endphp
            @foreach($data as $key => $order)
                @foreach($order->orderProductReturns as $sKey => $orderProduct)
                    <tr>
                        @if($sKey == 0)
                        <td rowspan="{{ $order->orderProductReturns->count() }}">{{ ($key+1) }}</td>
                        <td rowspan="{{ $order->orderProductReturns->count() }}">{{ $order->invoice }}</td>
                        <td rowspan="{{ $order->orderProductReturns->count() }}">{{ optional($order->branch)->name }}</td>
                        <td rowspan="{{ $order->orderProductReturns->count() }}">{{ optional($order->customer)->name }}</td>
                        @endif
                        <td>{{ date('d-m-Y', strtotime($orderProduct->created_at)) }}</td>
                        <td>{{ optional(optional($orderProduct->orderProduct)->product)->name }}</td>
                        <td>{{ round($orderProduct->quantity, 2) }}</td>
                        <td>{{ round($orderProduct->returnAmount, 2) }}</td>
                        <td>{{ $orderProduct->createdByUser->name }}</td>
                    </tr>
                    @php
                        $totalQuantity += round($orderProduct->quantity, 2);
                        $totalReturnAmount += round($orderProduct->returnAmount, 2);
                    @endphp
                @endforeach
            @endforeach
            <tr>
                <th>Total:</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>{{ $totalQuantity }}</th>
                <th>{{ $totalReturnAmount }}</th>
                <th></th>
            </tr>
            </tbody>
        </table>
@endsection
