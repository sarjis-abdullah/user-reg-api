<table>
    <tr>
        <th>Sales Person Report</th>
        <th>Print Date: {{ \Carbon\Carbon::now()->format('d M, Y, h:i:s A') }}</th>
    </tr>
</table>
@if((request()->filled('startDate') && request()->filled('endDate')) || request()->filled('salesPersonId'))
    <table style="margin-bottom: 10px">
        <tr>
            <th>
                @if(request()->filled('startDate') && request()->filled('endDate'))
                    Date: {{ date('d M, Y', strtotime(request()->get('startDate'))) .' to '. date('d M, Y', strtotime(request()->get('endDate'))) }}
                @endif
                @if(request()->filled('salesPersonId'))
                    @php
                        $employee = \App\Models\Employee::query()->with('user:id,name')->where('id', request()->get('salesPersonId'))->first();
                    @endphp
                    &nbsp;&nbsp;&nbsp; Sales Person: {{ ucfirst($employee->user->name) }}
                @endif
            </th>
        </tr>
    </table>
@endif
<table>
    <thead>
    <tr>
        <th>Sl No</th>
        <th>Salesman</th>
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
    @foreach(json_decode($data) as $key => $salesPerson)
        <tr>
            <td>{{ ($key+1) }}</td>
            <td>{{ $salesPerson->name }}</td>
            <td>{{ $salesPerson->orderAmount }}</td>
            <td>{{ $salesPerson->orderReturnAmount }}</td>
            <td>{{ $salesPerson->netTotalAmount }}</td>
        </tr>
        @php
            $total_amount += $salesPerson->orderAmount;
            $total_return_amount += $salesPerson->orderReturnAmount;
            $total_net_amount += $salesPerson->netTotalAmount;
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
