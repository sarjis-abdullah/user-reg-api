@extends('pdf.layout')
@section('title', 'Purchase Return Report')
@section('pdfContent')
    @if((request()->filled('startDate') && request()->filled('endDate')) || request()->filled('supplierId'))
    <table style="margin-bottom: 10px">
        <tr>
            <th>
                @if(request()->filled('startDate') && request()->filled('endDate'))
                    Date: {{ date('d M, Y', strtotime(request()->get('startDate'))) .' to '. date('d M, Y', strtotime(request()->get('endDate'))) }}
                @endif
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
                <th>REFERENCE</th>
                <th>BRANCH</th>
                <th>SUPPLIER</th>
                <th>RETURN DATE</th>
                <th>PRODUCT</th>
                <th>QUANTITY</th>
                <th>AMOUNT</th>
            </tr>
            </thead>
            <tbody>
            @php
                $totalQuantity = 0;
                $totalReturnAmount = 0;
            @endphp
            @foreach($data as $key => $purchase)
                @foreach($purchase->purchaseProductReturns as $sKey => $purchaseProduct)
                    <tr>
                        @if($sKey == 0)
                        <td rowspan="{{ $purchase->purchaseProductReturns->count() }}">{{ ($key+1) }}</td>
                        <td rowspan="{{ $purchase->purchaseProductReturns->count() }}">{{ $purchase->reference }}</td>
                        <td rowspan="{{ $purchase->purchaseProductReturns->count() }}">{{ optional($purchaseProduct->branch)->name }}</td>
                        <td rowspan="{{ $purchase->purchaseProductReturns->count() }}">{{ optional(optional(optional($purchaseProduct->purchaseProduct)->purchase)->supplier)->name }}</td>
                        @endif
                        <td>{{ date('d-m-Y', strtotime($purchaseProduct->created_at)) }}</td>
                        <td>{{ optional(optional($purchaseProduct->purchaseProduct)->product)->name }}</td>
                        <td>{{ round($purchaseProduct->quantity, 2) }}</td>
                        <td>{{ round($purchaseProduct->returnAmount, 2) }}</td>
                    </tr>
                    @php
                        $totalQuantity += round($purchaseProduct->quantity, 2);
                        $totalReturnAmount += round($purchaseProduct->returnAmount, 2);
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
            </tr>
            </tbody>
        </table>
@endsection
