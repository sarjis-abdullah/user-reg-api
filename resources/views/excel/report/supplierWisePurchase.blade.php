<table>
    <tr>
        <th>Supplier Wise Purchase Report</th>
        <th>Print Date: {{ \Carbon\Carbon::now()->format('d M, Y, h:i:s A') }}</th>
    </tr>
</table>
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
        <th>SUPPLIER</th>
        <th>TOTAL PURCHASE</th>
        <th>TOTAL PAID</th>
        <th>TOTAL DUE</th>
        <th>TOTAL DISCOUNT</th>
        <th>TOTAL TAX</th>
        <th>TOTAL RETURN</th>
        <th>PAYMENT STATUS</th>
        <th>BRANCH</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $key => $supplierPurchase)
        <tr>
            <td>{{ ($key+1) }}</td>
            <td>{{ $supplierPurchase->supplierName }}</td>
            <td>{{ round($supplierPurchase->totalPurchaseAmount, 2) }}</td>
            <td>{{ round($supplierPurchase->totalPaidAmount, 2) }}</td>
            <td>{{ round($supplierPurchase->totalDueAmount, 2) }}</td>
            <td>{{ round($supplierPurchase->totalDiscountAmount, 2) }}</td>
            <td>{{ round($supplierPurchase->totalTaxAmount, 2) }}</td>
            <td>{{ round($supplierPurchase->totalReturnAmount, 2) }}</td>
            <td>{{ \App\Models\Payment::paymentStatus($supplierPurchase->totalDueAmount, $supplierPurchase->totalPaidAmount) }}</td>
            <td>{{ $supplierPurchase->branchName }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
