<table>
    <tr>
        <th>Purchase Report</th>
        <th>Print Date: {{ \Carbon\Carbon::now()->format('d M, Y, h:i:s A') }}</th>
    </tr>
</table>
@if((request()->filled('startDate') && request()->filled('endDate')) || request()->filled('paymentStatusGroup') || request()->filled('supplierId'))
    <table style="margin-bottom: 10px">
        <tr>
            <th>
                @if(request()->filled('startDate') && request()->filled('endDate'))
                    Date: {{ date('d M, Y', strtotime(request()->get('startDate'))) .' to '. date('d M, Y', strtotime(request()->get('endDate'))) }}
                @endif
                @if(request()->filled('paymentStatusGroup'))
                    &nbsp;&nbsp;&nbsp; Payment Status: {{ ucfirst(request()->get('paymentStatusGroup')) }}
                @endif
                @if(request()->filled('supplierId'))
                    @php
                        $supplier = \App\Models\Supplier::query()->where('id', request()->get('supplierId'))->first(['name']);
                    @endphp
                    @if($supplier)
                        &nbsp;&nbsp;&nbsp; Supplier: {{ ucfirst($supplier->name) }}
                    @endif
                @endif
            </th>
        </tr>
    </table>
@endif
<table>
    <thead>
    <tr>
        <th>SL NO</th>
        <th>DATE</th>
        <th>REFERENCE</th>
        <th>SUPPLIER</th>
        <th>GRAND TOTAL</th>
        <th>RETURNED AMOUNT</th>
        <th>PAID</th>
        <th>DUE</th>
        <th>PURCHASE STATUS</th>
        <th>PAYMENT STATUS</th>
        <th>PURCHASE BY</th>
    </tr>
    </thead>
    <tbody>
    @php
        $totalGrandTotal = 0;
        $totalReturnedAmount = 0;
        $totalPaid = 0;
        $totalDue = 0;
    @endphp
    @foreach($data as $key => $purchase)
        <tr>
            <td>{{ ($key+1) }}</td>
            <td>
                {{ date('d-m-Y', strtotime($purchase->created_at)) }}<br>
                <small>{{ date('h:i:s A', strtotime($purchase->created_at)) }}</small>
            </td>
            <td>{{ $purchase->reference }}</td>
            <td>{{ optional($purchase->supplier)->name }}</td>
            <td>{{ round($purchase->totalAmount, 2) }}</td>
            <td>{{ round($purchase->returnedAmount, 2) }}</td>
            <td>{{ round($purchase->paid, 2) }}</td>
            <td>{{ round($purchase->due, 2) }}</td>
            <td>{{ ucfirst($purchase->status) }}</td>
            <td>{{ ucfirst($purchase->paymentStatus) }}</td>
            <td>{{ optional($purchase->createdByUser)->name }}</td>
        </tr>
        @php
            $totalGrandTotal += round($purchase->totalAmount, 2);
            $totalReturnedAmount += round($purchase->returnedAmount, 2);
            $totalPaid += round($purchase->paid, 2);
            $totalDue += round($purchase->due, 2);
        @endphp
    @endforeach
    <tr>
        <th>Total:</th>
        <th></th>
        <th></th>
        <th></th>
        <th>{{ $totalGrandTotal }}</th>
        <th>{{ $totalReturnedAmount }}</th>
        <th>{{ $totalPaid }}</th>
        <th>{{ $totalDue }}</th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
    </tbody>
</table>
