<table>
    <tr>
        <th>Payment Summary Report</th>
        <th>Print Date: {{ \Carbon\Carbon::now()->format('d M, Y, h:i:s A') }}</th>
    </tr>
</table>
@if((request()->filled('startDate') && request()->filled('endDate')) || request()->filled('paymentType') || request()->filled('paymentSource'))
    <table style="margin-bottom: 10px">
        <tr>
            <th>
                @if(request()->filled('startDate') && request()->filled('endDate'))
                    Date: {{ date('d M, Y', strtotime(request()->get('startDate'))) .' to '. date('d M, Y', strtotime(request()->get('endDate'))) }}
                @endif
                @if(request()->filled('paymentType'))
                    &nbsp;&nbsp;&nbsp; Payment Type: {{ ucfirst(request()->get('paymentType')) }}
                @endif
                @if(request()->filled('paymentSource'))
                    &nbsp;&nbsp;&nbsp; Payment Source: {{ ucfirst(\App\Services\Helpers\PdfHelper::paymentSource(request()->get('paymentSource'))) }}
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
        <th>PAYMENT SOURCE</th>
        <th>PAYMENT METHOD</th>
        <th>PAYMENT TYPE</th>
        <th>AMOUNT</th>
        <th>AUTHORIZE BY</th>
    </tr>
    </thead>
    <tbody>
    @php
        $totalDebit = 0;
        $totalCredit = 0;
    @endphp
    @foreach($data as $key => $paymentSummary)
        <tr>
            <td>{{ ($key+1) }}</td>
            <td>
                {{ date('d-m-Y', strtotime($paymentSummary->date)) }}
                <br>
                <small>{{ date('h:i A', strtotime($paymentSummary->date)) }}</small>
            </td>
            <td>
                @if($paymentSummary->paymentableType === \App\Models\Payment::PAYMENT_SOURCE_ORDER && $paymentSummary->payType === \App\Models\Payment::PAYMENT_SOURCE_ORDER)
                    {{ \App\Services\Helpers\PdfHelper::paymentSource($paymentSummary->paymentableType) }}
                @elseif($paymentSummary->paymentableType === \App\Models\Payment::PAYMENT_SOURCE_ORDER && $paymentSummary->payType === \App\Models\Payment::PAYMENT_SOURCE_ORDER_DUE)
                    Sale Due
                @elseif($paymentSummary->paymentableType === \App\Models\Payment::PAYMENT_SOURCE_PURCHASE && $paymentSummary->payType === \App\Models\Payment::PAYMENT_SOURCE_PURCHASE)
                    {{ \App\Services\Helpers\PdfHelper::paymentSource($paymentSummary->paymentableType) }}
                @elseif($paymentSummary->paymentableType === \App\Models\Payment::PAYMENT_SOURCE_PURCHASE && $paymentSummary->payType === \App\Models\Payment::PAYMENT_SOURCE_PURCHASE_DUE)
                    Purchase Due
                @endif
            </td>
            <td>{{ $paymentSummary->method }}</td>
            <td>{{ $paymentSummary->cashFlow == 'in' ? \App\Models\Payment::PAYMENT_TYPE_CREDIT : \App\Models\Payment::PAYMENT_TYPE_DEBIT }}</td>
            <td>{{ $paymentSummary->amount }}</td>
            <td>{{ optional($paymentSummary->createdByUser)->name }}</td>
        </tr>
        @php
            $totalDebit += $paymentSummary->cashFlow != 'in' ? $paymentSummary->amount : 0;
            $totalCredit += $paymentSummary->cashFlow == 'in' ? $paymentSummary->amount : 0;
        @endphp
    @endforeach
    @if(request()->filled('paymentType') && request()->get('paymentType') == 'debit')
        <tr>
            <td colspan="6" style="text-align: left;border-right: none;font-weight: bold">
                Total Debit:
            </td>
            <td style="text-align: right;border-left: none;">
                {{ $totalDebit }}
            </td>
        </tr>
    @elseif(request()->filled('paymentType') && request()->get('paymentType') == 'credit')
        <tr>
            <td colspan="6" style="text-align: left;border-right: none;font-weight: bold">
                Total Credit:
            </td>
            <td style="text-align: right;border-left: none;">
                {{ $totalCredit }}
            </td>
        </tr>
    @else
        <tr>
            <td colspan="6" style="text-align: left;border-right: none;font-weight: bold">
                Total Debit:
            </td>
            <td style="text-align: right;border-left: none;">
                {{ $totalDebit }}
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: left;border-right: none;font-weight: bold">
                Total Credit:
            </td>
            <td style="text-align: right;border-left: none;">
                {{ $totalCredit }}
            </td>
        </tr>
    @endif
    </tbody>
</table>
