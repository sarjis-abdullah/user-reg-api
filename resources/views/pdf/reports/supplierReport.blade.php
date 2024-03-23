@extends('pdf.layout')
@section('title', 'Supplier Report')
@section('pdfContent')
    @if((request()->filled('purchaseStartDate') && request()->filled('purchaseEndDate')) || request()->filled('id'))
    <table style="margin-bottom: 10px">
        <tr>
            <th>
                @if(request()->filled('purchaseStartDate') && request()->filled('purchaseEndDate'))
                    Date: {{ date('d M, Y', strtotime(request()->get('purchaseStartDate'))) .' to '. date('d M, Y', strtotime(request()->get('purchaseEndDate'))) }}
                @endif
                @if(request()->filled('id'))
                    @php
                        $supplier = \App\Models\Supplier::query()->where('id', request()->get('id'))->first(['name']);
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
                <th>SUPPLIER NAME</th>
                <th>EMAIL</th>
                <th>PHONE</th>
                <th>GRAND TOTAL</th>
                <th>PAID</th>
                <th>DUE</th>
                <th width="20%">PAYMENT STATUS</th>
            </tr>
            </thead>
            <tbody>
            @php
                $totalAmount = 0;
                $totalPaidAmount = 0;
                $totalDue = 0;
            @endphp
            @foreach($data as $key => $supplier)
                <tr>
                    <td>{{ ($key+1) }}</td>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->email }}</td>
                    <td>{{ $supplier->phone }}</td>
                    <td>{{ round($supplier->purchases_sum_total_amount, 2) }}</td>
                    <td>{{ round($supplier->purchases_sum_paid, 2) }}</td>
                    <td>{{ round($supplier->purchases_sum_due, 2) }}</td>
                    <td>{{ (new \App\Models\Supplier())->paymentStatus() }}</td>
                </tr>
                @php
                    $totalAmount += round($supplier->purchases_sum_total_amount, 2);
                    $totalPaidAmount += round($supplier->purchases_sum_paid, 2);
                    $totalDue += round($supplier->purchases_sum_due, 2);
                @endphp
            @endforeach
            <tr>
                <th>Total:</th>
                <th></th>
                <th></th>
                <th></th>
                <th>{{ $totalAmount }}</th>
                <th>{{ $totalPaidAmount }}</th>
                <th>{{ $totalDue }}</th>
                <th></th>
            </tr>
            </tbody>
        </table>
@endsection
