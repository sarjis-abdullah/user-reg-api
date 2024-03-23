@extends('pdf.layout')
@section('title', 'Customer Report')
@section('pdfContent')
    @if((request()->filled('startDate') && request()->filled('endDate')) || request()->filled('paymentStatusGroup') || request()->filled('id'))
    <table style="margin-bottom: 10px">
        <tr>
            <th>
                @if(request()->filled('orderStartDate') && request()->filled('orderEndDate'))
                    Date: {{ date('d M, Y', strtotime(request()->get('orderStartDate'))) .' to '. date('d M, Y', strtotime(request()->get('orderEndDate'))) }}
                @endif
                    @if(request()->filled('paymentStatusGroup'))
                        &nbsp;&nbsp;&nbsp; Payment Status: {{ ucfirst(request()->get('paymentStatusGroup')) }}
                    @endif
                @if(request()->filled('id'))
                    @php
                        $customer = \App\Models\Customer::query()->where('id', request()->get('id'))->first(['name']);
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
                <th>CUSTOMER NAME</th>
                <th>EMAIL</th>
                <th>PHONE</th>
                <th>GRAND TOTAL</th>
                <th>PAID</th>
                <th>DUE</th>
                <th>PAYMENT STATUS</th>
            </tr>
            </thead>
            <tbody>
            @php
                $totalAmount = 0;
                $totalPaidAmount = 0;
                $totalDue = 0;
            @endphp
            @foreach($data as $key => $customer)
                <tr>
                    <td>{{ ($key+1) }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ round($customer->orders_sum_amount, 2) }}</td>
                    <td>{{ round($customer->orders_sum_paid, 2) }}</td>
                    <td>{{ round($customer->orders_sum_due, 2) }}</td>
                    <td>{{ (new \App\Models\Customer())->paymentStatus() }}</td>
                </tr>
                @php
                    $totalAmount += round($customer->orders_sum_amount, 2);
                    $totalPaidAmount += round($customer->orders_sum_paid, 2);
                    $totalDue += round($customer->orders_sum_due, 2);
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
