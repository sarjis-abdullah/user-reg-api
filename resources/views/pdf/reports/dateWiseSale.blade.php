@extends('pdf.layout')
@section('title', 'Date Wise Sale Report')
@section('pdfContent')
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
                @if(!request()->filled('startMonth'))
                <th>DATE</th>
                @endif
                <th>SALE QUANTITY</th>
                <th>SALE AMOUNT</th>
                <th>RETURN AMOUNT</th>
                <th>NET SALES AMOUNT</th>
                <th>PAID AMOUNT</th>
                <th>Net PAID AMOUNT</th>
                <th>DUE AMOUNT</th>
                <th>DISCOUNT AMOUNT</th>
                <th>GROSS PROFIT</th>
            </tr>
            </thead>
            <tbody>
                @php
                    $totalSale = 0;
                    $totalSaleAmount = 0;
                    $totalReturnAmount = 0;
                    $totalNetSaleAmount = 0;
                    $totalPaidAmount = 0;
                    $totalNetPaidAmount = 0;
                    $totalDueAmount = 0;
                    $totalDiscountAmount = 0;
                    $totalGrossProfitAmount = 0;
                @endphp
                @foreach($data as $key => $sales)
                    <tr>
                        <td>{{ ($key+1) }}</td>
                        @if(!request()->filled('startMonth'))
                            <td>{{ isset($sales->date) ? $sales->date : '' }}</td>
                        @endif
                        <td>{{ $sales->totalSales }}</td>
                        <td>{{ $sales->totalSaleAmount }}</td>
                        <td>{{ round($sales->totalReturnAmount, 2) }}</td>
                        <td>{{ round($sales->totalSaleAmount - $sales->totalReturnAmount, 2) }}</td>
                        <td>{{ $sales->totalPaidAmount }}</td>
                        <td>{{ round($sales->totalPaidAmount - $sales->totalReturnAmount, 2) }}</td>
                        <td>{{ $sales->totalDueAmount }}</td>
                        <td>{{ $sales->totalDiscountAmount }}</td>
                        <td>{{ round($sales->totalGrossProfitAmount - ($sales->totalReturnProfitAmount - $sales->totalReturnDiscountAmount), 2) }}</td>
                    </tr>
                    @php
                        $totalSale += $sales->totalSales;
                        $totalSaleAmount += $sales->totalSaleAmount;
                        $totalReturnAmount += round($sales->totalReturnAmount, 2);
                        $totalNetSaleAmount += round($sales->totalSaleAmount - $sales->totalReturnAmount, 2);
                        $totalPaidAmount += $sales->totalPaidAmount;
                        $totalNetPaidAmount += round($sales->totalPaidAmount - $sales->totalReturnAmount, 2);
                        $totalDueAmount += $sales->totalDueAmount;
                        $totalDiscountAmount += $sales->totalDiscountAmount;
                        $totalGrossProfitAmount += round($sales->totalGrossProfitAmount - ($sales->totalReturnProfitAmount - $sales->totalReturnDiscountAmount), 2);
                    @endphp
                @endforeach
            <tr>
                <th>Total:</th>
                @if(!request()->filled('startMonth'))
                <th></th>
                @endif
                <th>{{ $totalSale }}</th>
                <th>{{ $totalSaleAmount }}</th>
                <th>{{ $totalReturnAmount }}</th>
                <th>{{ $totalNetSaleAmount }}</th>
                <th>{{ $totalPaidAmount }}</th>
                <th>{{ $totalNetPaidAmount }}</th>
                <th>{{ $totalDueAmount }}</th>
                <th>{{ $totalDiscountAmount }}</th>
                <th>{{ $totalGrossProfitAmount }}</th>
            </tr>
            </tbody>
        </table>
@endsection
