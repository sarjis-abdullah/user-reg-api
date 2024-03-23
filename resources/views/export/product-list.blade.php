@extends('pdf.layout')
@section('title', 'Product Report')
@section('pdfContent')
    <table>
        <thead>
        <tr>
            <th>SL NO</th>
            <th>PRODUCT NAME</th>
            <th>BARCODE</th>
            <th>SKU</th>
            <th>BRANCH</th>
            <th>PURCHASE PRICE</th>
            <th>SELLING PRICE</th>
            <th>QUANTITY</th>
            <th>EXPIRED DATE</th>
        </tr>
        </thead>
        <tbody>
        @foreach($products as $key => $product)
            @if(count($product) > 1)
                @foreach($product as $stockKey => $stock)
                    <tr>
                        @if(count($product) > 1 && $stockKey == 0)
                            <td rowspan="{{ count($product) }}">{{ ((int) $serial + $key) }}</td>
                            <td rowspan="{{ count($product) }}">{{ $stock['name'] }}</td>
                            <td rowspan="{{ count($product) }}">{{ $stock['barcode'] }}</td>
                        @elseif(count($product) == 1)
                            <td rowspan="{{ count($product) }}">{{ ((int) $serial + $key) }}</td>
                            <td rowspan="{{ count($product) }}">{{ $stock['name'] }}</td>
                            <td rowspan="{{ count($product) }}">{{ $stock['barcode'] }}</td>
                        @endif
                        <td>{{ $stock['sku'] }}</td>
                        <td>{{ $stock['branchName'] }}</td>
                        <td>{{ $stock['unitCost'] }}</td>
                        <td>{{ $stock['unitPrice'] }}</td>
                        <td>{{ $stock['quantity'] }}</td>
                        <td>{{ $stock['expiredDate'] }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>{{ ((int) $serial + $key) }}</td>
                    <td>{{ $product[0]['name'] }}</td>
                    <td>{{ $product[0]['barcode'] }}</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
@endsection
