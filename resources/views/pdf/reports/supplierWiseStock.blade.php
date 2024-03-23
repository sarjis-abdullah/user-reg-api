@extends('pdf.layout')
@section('title', 'Supplier Wise Purchase Report')
@section('pdfContent')
    @if(request()->filled('supplierId'))
    <table style="margin-bottom: 10px">
        <tr>
            <th>
                @if(request()->filled('supplierId'))
                    @php
                        $supplier = \App\Models\Supplier::query()->where('id', request()->get('supplierId'))->first(['name']);
                    @endphp
                        &nbsp;&nbsp;&nbsp; Supplier: {{ ucfirst($supplier->name) }}
                @endif
            </th>
        </tr>
    </table>
    @endif
    <table>
            <thead>
            <tr>
                <th>SL NO</th>
                <th>SUPPLIER</th>
                <th>PURCHASE QUANTITY</th>
                <th>SOLD QUANTITY</th>
                <th>PURCHASE RETURN QUANTITY</th>
                <th>SALE RETURN QUANTITY</th>
                <th>QUANTITY</th>
                <th>PURCHASE AMOUNT</th>
                <th>SELLING AMOUNT</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $key => $supplierStock)
                <tr>
                    <td>{{ ($key+1) }}</td>
                    <td>{{ $supplierStock->supplierName }}</td>
                    <td>{{ round($supplierStock->totalPurchaseQuantity, 2) }}</td>
                    <td>{{ round($supplierStock->totalSoldQuantity, 2) }}</td>
                    <td>{{ round($supplierStock->totalPurchaseReturnQuantity, 2) }}</td>
                    <td>{{ round($supplierStock->totalSaleReturnQuantity    , 2) }}</td>
                    <td>{{ round($supplierStock->totalStockLeft, 2) }}</td>
                    <td>{{ round($supplierStock->totalLeftStockPurchaseCost, 2) }}</td>
                    <td>{{ round($supplierStock->totalLeftStockSellingValue, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
@endsection
