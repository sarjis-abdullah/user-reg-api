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
        @foreach($data as $key => $product)
            @php
                if (request()->get('branchId')){
                    $branchStock = $product->stocks->where('branchId', request()->get('branchId'));
                }else{
                    $branchStock = $product->stocks;
                }
            @endphp
            @if($branchStock->count())
                @foreach($branchStock as $stockKey => $stocks)
                    <tr>
                        @if($branchStock->count() > 1 && $stockKey == 0)
                            <td rowspan="{{ $branchStock->count() }}">{{ ($key+1) }}</td>
                            <td rowspan="{{ $branchStock->count() }}">{{ $product->name }}</td>
                            <td rowspan="{{ $branchStock->count() }}">{{ $product->barcode }}</td>
                        @elseif($branchStock->count() == 1)
                            <td rowspan="{{ $branchStock->count() }}">{{ ($key+1) }}</td>
                            <td rowspan="{{ $branchStock->count() }}">{{ $product->name }}</td>
                            <td rowspan="{{ $branchStock->count() }}">{{ $product->barcode }}</td>
                        @endif
                        <td>{{ $stocks->sku }}</td>
                        <td>{{ optional($all_branch->where('id', $stocks->branchId)->first())->name }}</td>
                        <td>{{ $stocks->unitCost }}</td>
                        <td>{{ $stocks->unitPrice }}</td>
                        <td>{{ $stocks->quantity }}</td>
                        <td>{{ $stocks->expiredDate }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>{{ ($key+1) }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->barcode }}</td>
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
