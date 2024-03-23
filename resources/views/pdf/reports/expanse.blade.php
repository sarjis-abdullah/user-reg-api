@extends('pdf.layout')
@section('title', 'Expense Report')
@section('pdfContent')
    @if((request()->filled('startDate') && request()->filled('endDate')) || request()->filled('categoryId'))
        <table style="margin-bottom: 10px">
            <tr>
                <th>
                    @if(request()->filled('startDate') && request()->filled('endDate'))
                        Date: {{ date('d M, Y', strtotime(request()->get('startDate'))) .' to '. date('d M, Y', strtotime(request()->get('endDate'))) }}
                    @endif
                    @if(request()->filled('categoryId'))
                        @php
                            $expanseCategory = \App\Models\ExpenseCategory::query()->where('id', request()->get('categoryId'))->first(['name']);
                        @endphp
                        &nbsp;&nbsp;&nbsp; Expense Category: {{ ucfirst($expanseCategory->name) }}
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
            <th>EXPENSE CATEGORIES</th>
            <th>TOTAL EXPANSE</th>
            <th>EXPENSE REASON</th>
            <th>RESPONSIBLE PERSON</th>
        </tr>
        </thead>
        <tbody>
        @php
            $totalAmount = 0;
        @endphp
        @foreach(json_decode($data) as $key => $expanse)
            <tr>
                <td>{{ ($key+1) }}</td>
                <td>
                    {{ date('d-m-Y', strtotime($expanse->created_at)) }}<br>
                    <small>{{ date('h:i:s A', strtotime($expanse->created_at)) }}</small>
                </td>
                <td>{{ $expanse->category->name }}</td>
                <td>{{ $expanse->amount }}</td>
                <td>{{ $expanse->expenseReason }}</td>
                <td>{{ $expanse->createdByUser->name }}</td>
            </tr>
            @php
                $totalAmount += $expanse->amount;
            @endphp
        @endforeach
        <tr>
            <th colspan="3">Total:</th>
            <th>{{ $totalAmount }}</th>
            <th colspan="2"></th>
        </tr>
        </tbody>
    </table>
@endsection
