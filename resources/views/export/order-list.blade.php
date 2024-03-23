@extends('pdf.layout')
@section('title', 'Order Report')
@section('pdfContent')
    <table>
        <thead>
        <tr>
            <th>SL.</th>
            <th>INVOICE</th>
            <th>DATE</th>
            <th>BRANCH</th>
            <th>SALE PERSON</th>
            <th>CUSTOMER</th>
            <th>TAX</th>
            <th>SHIPPING COST</th>
            <th>DISCOUNT</th>
            <th>AMOUNT</th>
            <th>DUE</th>
            <th>PAID</th>
            <th>PROFIT AMOUNT</th>
            <th>DELIVERY METHOD</th>
            <th>PAYMENT METHODS</th>
            <th>PAYMENT STATUS</th>
            <th>STATUS</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $key => $item)
            <tr>
                <td>{{ ((int) $serial + $key) }}</td>
                <td>{{ $item['invoice'] }}</td>
                <td>{{  \Carbon\Carbon::parse($item['date'])->format('d-m-Y H:i:s') }}</td>
                <td>{{ $item['branchName'] }}</td>
                <td>{{ $item['salePersonName'] }}</td>
                <td>{{ $item['customerName'] }}</td>
                <td>{{ $item['tax'] }}</td>
                <td>{{ $item['shippingCost'] }}</td>
                <td>{{ $item['discount'] }}</td>
                <td>{{ $item['amount'] }}</td>
                <td>{{ $item['due'] }}</td>
                <td>{{ $item['paid'] }}</td>
                <td>{{ $item['profitAmount'] }}</td>
                <td>{{ $item['deliveryMethod'] }}</td>
                <td>{{ $item['paymentMethods'] }}</td>
                <td>{{ $item['paymentStatus'] }}</td>
                <td>{{ $item['status'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
