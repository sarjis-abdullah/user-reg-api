@extends('pdf.layout')
@section('title', 'Adjustment Report')
@section('pdfContent')
    <table>
        <thead>
        <tr>
            <th>SL.</th>
            <th>DATE</th>
            <th>BARCODE</th>
            <th>PRODUCT NAME</th>
            <th>BRANCH</th>
            <th>QUANTITY</th>
            <th>UNIT COST</th>
            <th>TOTAL</th>
            <th>TYPE</th>
            <th>ADJUSTMENT BY</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $key => $item)
            <tr>
                <td>{{ ((int) $serial + $key) }}</td>
                <td>{{  \Carbon\Carbon::parse($item['date'])->format('d-m-Y') }}</td>
                <td>{{ $item['barcode'] }}</td>
                <td>{{ $item['productName'] }}</td>
                <td>{{ $item['branchName'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ $item['unitCost'] }}</td>
                <td>{{ $item['total'] }}</td>
                <td>{{ $item['type'] }}</td>
                <td>{{ $item['adjustmentBy'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
