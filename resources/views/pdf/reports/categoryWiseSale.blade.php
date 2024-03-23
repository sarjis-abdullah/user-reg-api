@extends('pdf.layout')
@section('title', 'Category Wise Sale Report')
@section('pdfContent')
    @if((request()->filled('startDate') && request()->filled('endDate')) || request()->filled('categoryId'))
    <table style="margin-bottom: 10px">
        <tr>
            <th>
                @if(request()->filled('startDate') && request()->filled('endDate'))
                    Date: {{ date('d M, Y', strtotime(request()->get('startDate'))) .' to '. date('d M, Y', strtotime(request()->get('endDate'))) }}
                @endif
                @if(request()->filled('categoryId'))
                    @php
                        $category = \App\Models\Category::query()->where('id', request()->get('categoryId'))->first(['name']);
                    @endphp
                    @if($category)
                            &nbsp;&nbsp;&nbsp; Category: {{ ucfirst($category->name) }}
                    @endif
                @endif
            </th>
        </tr>
    </table>
    @endif
    <table>
            <thead>
            <tr>
                <th>SL NO</th>
                <th>CATEGORY</th>
                <th>SALE QUANTITY</th>
                <th>RETURN QUANTITY</th>
                <th>NET SALE QUANTITY</th>
                <th>SALES AMOUNT</th>
                <th>RETURN AMOUNT</th>
                <th>NET SALES AMOUNT</th>
                <th>GROSS PROFIT</th>
            </tr>
            </thead>
            <tbody>
            @php
                $totalQty = 0;
                $totalSaleQty = 0;
                $totalReturnQty = 0;
                $totalSalesAmount = 0;
                $totalReturnAmount = 0;
                $totalNetSalesAmount = 0;
                $totalGrossProfit = 0;
            @endphp
            @foreach(json_decode($data) as $key => $category)
                <tr>
                    <td>{{ ($key+1) }}</td>
                    <td>{{ $category->categoryName }}</td>
                    <td>{{ round($category->quantity, 2) }}</td>
                    <td>{{ round($category->returnQuantity, 2) }}</td>
                    <td>{{ round($category->netSaleQuantity, 2) }}</td>
                    <td>{{ round($category->soldAmount, 2) }}</td>
                    <td>{{ round($category->returnAmount, 2) }}</td>
                    <td>{{ round($category->netTotalAmount, 2) }}</td>
                    <td>{{ round($category->grossProfitAmount, 2) }}</td>
                </tr>
                @php
                    $totalQty += round($category->netSaleQuantity, 2);
                    $totalSaleQty += round($category->quantity, 2);
                    $totalReturnQty += round($category->returnQuantity, 2);
                    $totalSalesAmount += round($category->soldAmount, 2);
                    $totalReturnAmount += round($category->returnAmount, 2);
                    $totalNetSalesAmount += round($category->netTotalAmount, 2);
                    $totalGrossProfit += round($category->grossProfitAmount, 2);
                @endphp
            @endforeach
            <tr>
                <th>Total:</th>
                <th></th>
                <th>{{ $totalSaleQty }}</th>
                <th>{{ $totalReturnQty }}</th>
                <th>{{ $totalQty }}</th>
                <th>{{ $totalSalesAmount }}</th>
                <th>{{ $totalReturnAmount }}</th>
                <th>{{ $totalNetSalesAmount }}</th>
                <th>{{ $totalGrossProfit }}</th>
            </tr>
            </tbody>
        </table>
@endsection
