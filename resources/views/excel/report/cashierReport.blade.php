<table>
    <tr>
        <th>Cashier Report</th>
        <th>Print Date: {{ \Carbon\Carbon::now()->format('d M, Y, h:i:s A') }}</th>
    </tr>
</table>
@if((request()->filled('startDate') && request()->filled('endDate')) || request()->filled('cashierId'))
    <table style="margin-bottom: 10px">
        <tr>
            <th>
                @if(request()->filled('startDate') && request()->filled('endDate'))
                    Date: {{ date('d M, Y', strtotime(request()->get('startDate'))) .' to '. date('d M, Y', strtotime(request()->get('endDate'))) }}
                @endif
                @if(request()->filled('cashierId'))
                    @php
                        $cashier = \App\Models\Manager::query()->with('user:id,name')->where('id', request()->get('cashierId'))->first();
                    @endphp
                    @if($cashier)
                            &nbsp;&nbsp;&nbsp; Cashier Name : {{ ucfirst($cashier->user->name) }}
                    @endif
                @endif
            </th>
        </tr>
    </table>
@endif
<table>
    <thead>
    <tr>
        <th>Sl No</th>
        <th>Cashier</th>
        <th>Sale Amount</th>
        <th>Return Amount</th>
        <th>Net Sales Amount</th>
    </tr>
    </thead>
    <tbody>
    @php
        $total_amount = 0;
        $total_return_amount = 0;
        $total_net_amount = 0;
    @endphp
    @foreach(json_decode($data) as $key => $cashier)
        <tr>
            <td>{{ ($key+1) }}</td>
            <td>{{ $cashier->name }}</td>
            <td>{{ $cashier->orderAmount }}</td>
            <td>{{ $cashier->orderReturnAmount }}</td>
            <td>{{ $cashier->netTotalAmount }}</td>
        </tr>
        @php
            $total_amount += $cashier->orderAmount;
            $total_return_amount += $cashier->orderReturnAmount;
            $total_net_amount += $cashier->netTotalAmount;
        @endphp
    @endforeach
    <tr>
        <th>Total:</th>
        <th></th>
        <th>{{ $total_amount }}</th>
        <th>{{ $total_return_amount }}</th>
        <th>{{ $total_net_amount }}</th>
    </tr>
    </tbody>
</table>
