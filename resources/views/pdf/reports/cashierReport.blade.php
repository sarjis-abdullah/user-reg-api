@extends('pdf.layout')
@section('title', 'Cashier Report')
@section('pdfContent')
    @if((request()->filled('startDate') && request()->filled('endDate')) || request()->filled('cashierId'))
    <table style="margin-bottom: 10px">
        <tr>
            <th>
                @if(request()->filled('startDate') && request()->filled('endDate'))
                    Date: {{ date('d M, Y', strtotime(request()->get('startDate'))) .' to '. date('d M, Y', strtotime(request()->get('endDate'))) }}
                @endif
                @if(request()->filled('cashierId'))
                    @php
                        $cashier = \App\Models\Manager::query()->with('user:id,name')->where('id', request()->get('cashierId'))->first();
                    @endphp
                        &nbsp;&nbsp;&nbsp; Cashier Name : {{ ucfirst($cashier->user->name) }}
                @endif
            </th>
        </tr>
    </table>
    @endif
    <table>
            <thead>
            <tr>
                <th>Sl No</th>
                <th>Cashier</th>
                <th>Sale Amount</th>
                <th>Return Amount</th>
                <th>Net Sales Amount</th>
            </tr>
            </thead>
            <tbody>
            @php
                $total_amount = 0;
                $total_return_amount = 0;
                $total_net_amount = 0;
            @endphp
            @foreach(json_decode($data) as $key => $cashierInfo)
                <tr>
                    <td>{{ ($key+1) }}</td>
                    <td>{{ $cashierInfo->name }}</td>
                    <td>{{ $cashierInfo->orderAmount }}</td>
                    <td>{{ $cashierInfo->orderReturnAmount }}</td>
                    <td>{{ $cashierInfo->netTotalAmount }}</td>
                </tr>
                @php
                    $total_amount += $cashierInfo->orderAmount;
                    $total_return_amount += $cashierInfo->orderReturnAmount;
                    $total_net_amount += $cashierInfo->netTotalAmount;
                @endphp
            @endforeach
            <tr>
                <th>Total:</th>
                <th></th>
                <th>{{ $total_amount }}</th>
                <th>{{ $total_return_amount }}</th>
                <th>{{ $total_net_amount }}</th>
            </tr>
            </tbody>
        </table>
@endsection
