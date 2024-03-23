@extends('pdf.layout')
@section('title', 'Stock Report')
@section('pdfContent')
    <table>
        <thead>
        <tr>
            <th>Sl.</th>
            <th>Product Name</th>
            <th>SKU</th>
            <th>Expired Date</th>
            <th>Branch</th>
            <th>Current Stock</th>
            <th>Cost Price</th>
            <th>Stock Cost Value</th>
            <th>Sale Price</th>
            <th>Stock Sale Value</th>
        </tr>
        </thead>
        <tbody>
        @php
            $totalStockQuantity = 0;
            $totalStockValue = 0;
            $totalStockSaleValue = 0;
            $i = 0;
        @endphp
        @foreach($products as $key => $product)
            @foreach($product as $stockKey => $stock)
                @php
                    $i = $i + 1
                @endphp
                <tr>
                    {{--@if(count($product) > 1 && $stockKey == 0)
                        <td rowspan="{{ count($product) }}">{{ ((int) $serial + $key) }}</td>
                        <td rowspan="{{ count($product) }}">{{ $stock['productName'] }}</td>
                    @elseif(count($product) == 1)
                        <td rowspan="{{ count($product) }}">{{ ((int) $serial + $key) }}</td>
                        <td rowspan="{{ count($product) }}">{{ $stock['productName'] }}</td>
                    @endif--}}
                        <td>{{ $i }}</td>
                        <td>{{ $stock['productName'] }}</td>
                        <td>{{ $stock['sku'] }}</td>
                        <td>{{ $stock['expiredDate'] }}</td>
                        <td>{{ $stock['branchName'] }}</td>
                        <td>{{ $stock['currentStock'] }}</td>
                        <td>{{ $stock['costPrice'] }}</td>
                        <td>{{ $stock['stockCostValue'] }}</td>
                        <td>{{ $stock['salePrice'] }}</td>
                        <td>{{ $stock['stockSaleValue'] }}</td>
                </tr>
                @php
                    $totalStockQuantity += $stock['currentStock'];
                    $totalStockValue += $stock['stockCostValue'];
                    $totalStockSaleValue += $stock['stockSaleValue'];
                @endphp
            @endforeach
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th colspan="5" style="text-align: left">Total:</th>
            <th>{{ $totalStockQuantity }}</th>
            <th></th>
            <th>{{ $totalStockValue }}</th>
            <th></th>
            <th>{{ $totalStockSaleValue }}</th>
        </tr>
        </tfoot>
    </table>
@endsection
