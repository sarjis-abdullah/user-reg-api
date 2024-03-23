@extends('pdf.layout')
@section('title', 'Stock Transfer report')
@section('pdfContent')
    @if((request()->filled('startDate') && request()->filled('endDate')) || request()->filled('fromBranchId') || request()->filled('toBranchId') || request()->filled('status'))
    <table style="margin-bottom: 10px">
        <tr>
            <th>
                @if(request()->filled('startDate') && request()->filled('endDate'))
                    Date: {{ date('d M, Y', strtotime(request()->get('startDate'))) .' to '. date('d M, Y', strtotime(request()->get('endDate'))) }}
                @endif
                @if(request()->filled('fromBranchId'))
                    @php
                        $fromBranch = \App\Models\Branch::query()->where('id', request()->get('fromBranchId'))->first(['name']);
                    @endphp
                    @if($fromBranch)
                            &nbsp;&nbsp;&nbsp; From Branch: {{ ucfirst($fromBranch->name) }}
                    @endif
                @endif
                @if(request()->filled('toBranchId'))
                    @php
                        $toBranch = \App\Models\Branch::query()->where('id', request()->get('toBranchId'))->first(['name']);
                    @endphp
                    @if($toBranch)
                            &nbsp;&nbsp;&nbsp; To Branch: {{ ucfirst($toBranch->name) }}
                    @endif
                @endif
                @if(request()->filled('status'))
                        &nbsp;&nbsp;&nbsp; Status: {{ ucfirst(request()->get('status')) }}
                @endif
            </th>
        </tr>
    </table>
    @endif
    <table>
            <thead>
            <tr>
                <th>Sl No</th>
                <th>Date With Time</th>
                <th>Reference No</th>
                <th>Total Unit Cost</th>
                <th>Total Selling Amount</th>
                <th>From Branch</th>
                <th>To Branch</th>
                <th>Status</th>
                <th>Transferred By</th>
            </tr>
            </thead>
            <tbody>
                @php
                    $totalCostPrice = 0;
                    $totalSalePrice = 0;
                @endphp
                @foreach(json_decode($data) as $key => $stockTransfer)
                    <tr>
                        <td>{{ ($key+1) }}</td>
                        <td>
                            {{ date('d-m-Y', strtotime($stockTransfer->created_at)) }}
                            <br>
                            {{ date('h:i A', strtotime($stockTransfer->created_at)) }}
                        </td>
                        <td>{{ $stockTransfer->referenceNumber }}</td>
                        <td>{{ $stockTransfer->totalAmount }}</td>
                        <td>{{ $stockTransfer->totalSellingAmount }}</td>
                        <td>{{ $stockTransfer->fromBranch->name }}</td>
                        <td>{{ $stockTransfer->toBranch->name }}</td>
                        <td>{{ $stockTransfer->status }}</td>
                        <td>{{ $stockTransfer->createdByUser->name }}</td>
                    </tr>
                    @php
                        $totalCostPrice += $stockTransfer->totalAmount;
                        $totalSalePrice += $stockTransfer->totalSellingAmount;
                    @endphp
                @endforeach
                <tr>
                    <th>Total:</th>
                    <th colspan="2"></th>
                    <th>{{ $totalCostPrice }}</th>
                    <th>{{ $totalSalePrice }}</th>
                    <th colspan="4"></th>
                </tr>
            </tbody>
        </table>
@endsection
