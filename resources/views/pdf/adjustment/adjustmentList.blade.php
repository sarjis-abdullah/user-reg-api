@extends('pdf.layout')
@section('title', 'Adjustment List')
@section('pdfContent')
    <table>
        <thead>
        <tr>
            <th>Sl</th>
            <th>Date</th>
            <th>Name</th>
            <th>BARCODE</th>
            <th>Branch</th>
            <th>Quantity</th>
            <th>Total cost value</th>
            <th>Total sale value</th>
            <th>Reason</th>
            <th>Type</th>
            <th>Adjustment By</th>
        </tr>
        </thead>
        <tbody>
        @php
            $totalUnitCost = 0;
            $totalSale = 0;
        @endphp
        @foreach($data as $key => $adjustmentList)
            <tr>
                <td>{{ ($key+1) }}</td>
                <td>{{ date('d-m-Y',strtotime($adjustmentList->date)) }} {{ date('h:i:s ',strtotime($adjustmentList->created_at)) }}</td>
                <td>{{ optional(optional($adjustmentList->stock)->product)->name }}</td>
                <td>{{ optional(optional($adjustmentList->stock)->product)->barcode }}</td>
                <td>{{optional($adjustmentList->branch)->name}}</td>
                <td>{{$adjustmentList->quantity}}</td>
                <td>{{ ($adjustmentList->quantity * optional($adjustmentList->stock)->unitCost) }}</td>
                <td>{{ ($adjustmentList->quantity * optional($adjustmentList->stock)->unitPrice) }}</td>
                <td>{{$adjustmentList->reason}}</td>
                <td>{{$adjustmentList->type}}</td>
                <td>{{$adjustmentList->adjustmentBy}}</td>
            </tr>
            @php
                $totalUnitCost += ($adjustmentList->quantity * optional($adjustmentList->stock)->unitCost);
                $totalSale += ($adjustmentList->quantity * optional($adjustmentList->stock)->unitPrice);
            @endphp
        @endforeach
        <tr>
            <th>Total:</th>
            <th colspan="5"></th>
            <th>{{ $totalUnitCost }}</th>
            <th>{{ $totalSale }}</th>
            <th colspan="3"></th>
        </tr>
        </tbody>
    </table>
@endsection
