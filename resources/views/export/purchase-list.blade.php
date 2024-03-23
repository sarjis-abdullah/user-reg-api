@extends('pdf.layout')
@section('title', 'Purchase Report')
@section('pdfContent')
    <table>
        <thead>
        <tr>
            <th>SL.</th>
            <th>DATE</th>
            <th>REF</th>
            <th>SUPPLIER</th>
            <th>BRANCH</th>
            <th>TOTAL AMOUNT</th>
            <th>DISCOUNT</th>
            <th>SHIPPING COST</th>
            <th>DUE</th>
            <th>PAID</th>
            <th>STATUS</th>
            <th>PAYMENT STATUS</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $key => $item)
            <tr>
                <td>{{ ((int) $serial + $key) }}</td>
                <td>{{  \Carbon\Carbon::parse($item['date'])->format('d-m-Y') }}</td>
                <td>{{ $item['reference'] }}</td>
                <td>{{ $item['supplierName'] }}</td>
                <td>{{ $item['branchName'] }}</td>
                <td>{{ $item['totalAmount'] }}</td>
                <td>{{ $item['discountAmount'] }}</td>
                <td>{{ $item['shippingCost'] }}</td>
                <td>{{ $item['due'] }}</td>
                <td>{{ $item['paid'] }}</td>
                <td>{{ $item['status'] }}</td>
                <td>{{ $item['paymentStatus'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
