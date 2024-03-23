<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
        }

        * {
            font-family: Verdana, Arial, Tahoma, 'bangla', sans-serif;
            font-size: 10px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 0.01em solid #000000;
            padding: 6px;
            font-size: 10px;
        }

        th {
            background-color: #ddd;
            font-weight: bold;
            text-align: left;
        }

        .main {
            width: 100%;
        }

        .table-title {
            width: 50%;
            float: left;
            text-align: left;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .print-history {
            width: 50%;
            float: left;
            text-align: right;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .header {
            text-align: center;
        }
    </style>
</head>
<body>
@php
    $allData = json_decode($data);
    $data = $allData->order_data;
    $invoice_setting = $allData->invoice_setting;
@endphp
{{--@dd($invoice_setting)--}}
<htmlpageheader name="page-header">
    <div class="main">
        <div class="header">
            @if(\App\Services\Helpers\AppSettingHelper::logoUrl())
            <img style="margin-top: 20px;max-width: 130px;max-height:30px" src="{{ \App\Services\Helpers\AppSettingHelper::logoUrl() }}">
            @endif
            <p>{{ $data->branch->name }} <br> Sale Details</p>
        </div>
    </div>
</htmlpageheader>


<div style="width: 100%;font-size: 11px">
    <div style="width: 30%; float: left">
        <div style="margin-bottom: 10px">
            <b>Invoice No: {{ $data->invoice }}</b>
            <br>
            @if(isset($data->invoiceImage->src))
                <img height="30px" width="150px" src="{{ $data->invoiceImage->src }}">
            @endif
        </div>

        <article>
            Payment Status: {{ ucfirst($data->paymentStatus) }}
            <br>
            Sale Status: {{ ucfirst($data->status) }}
            <br>
            Date : {{  \Carbon\Carbon::parse($data->created_at)->format('M d, Y h:i A') }}

        </article>

    </div>
    <div style="width: 40%;float: left">
        <div style="width: 80%">
            <b>Branch Info</b>
            <article>
                {{ $data->branch->address }}
                <br>
                Phone Number: {{ $data->branch->phone }}
                @if(isset($invoice_setting->vatRegNo) && $invoice_setting->vatRegNo !== '')
                    <br>
                    Vat Reg No : {{  $invoice_setting->vatRegNo }}
                @endif
                @if(isset($invoice_setting->mushakNo) && $invoice_setting->mushakNo !== '')
                    <br>
                    Mushak : {{ $invoice_setting->mushakNo }}
                @endif
            </article>
        </div>
    </div>
    <div style="width: 30%;float: left;">
        <div>
            <b>Customer Info</b>
            <article>
                Name: {{ $data->customer->name }} <br>
                @if(isset($data->customer->phone))
                    Phone: {{ $data->customer->phone }} <br>
                @endif
                @if(isset($data->customer->address))
                    Address: {{ $data->customer->address }} <br>
                @endif
            </article>
        </div>
    </div>
</div>
<div style="width: 100%">
    <p style="font-weight: bold">Sale Summary</p>
    <table style="width: 100%;text-align: left">
        <thead>
        <tr>
            <th>Sl</th>
            <th width="25%">Product</th>
            <th>Code</th>
            <th style="text-align: center">Quantity</th>
            <th style="text-align: center">Price</th>
            <th style="text-align: center">Discounted Price</th>
            <th style="text-align: center">Discount</th>
            <th style="text-align: center">Vat</th>
            <th style="text-align: center">Subtotal</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data->orderProducts as $sl => $orderProducts)
            <tr>
                <td>{{ ($sl+1) }}</td>
                <td>{{ $orderProducts->product->name }}</td>
                <td>{{ $orderProducts->product->barcode }}</td>
                <td style="text-align: right">{{ $orderProducts->quantity }}</td>
                <td style="text-align: right">{{ $orderProducts->unitPrice }}</td>
                <td style="text-align: right">{{ $orderProducts->discountedUnitPrice }}</td>
                <td style="text-align: right">{{ $orderProducts->discount }}</td>
                <td style="text-align: right">{{ $orderProducts->tax }}</td>
                <td style="text-align: right">{{ $orderProducts->amount }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if($data->paid > 0)
        <p style="font-weight: bold">Payment Summary</p>
        <table style="width: 100%;text-align: left">
            <thead>
            <tr>
                <th>Date</th>
                <th>Payment Method</th>
                <th>Paid</th>
                <th>Transaction Number</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data->payments as $payment)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($payment->date)->format('M d, Y h:i A') }}</td>
                    <td>{{ $payment->method }}</td>
                    <td>{{ $payment->amount }}</td>
                    <td>{{ $payment->txnNumber }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

</div>

<div style="width: 100%;margin-top: 20px">
    <div style="width: 70%; float: left">
        &nbsp;
    </div>
    <div style="width: 30%; float: left">
        <table style="width: 100%; text-align: left;">
            @if($data->due)
                <tr>
                    <td style="border: 0; padding: 6px; font-size: 10px;">Due</td>
                    <td style="text-align: right;font-weight: bold;border: 0; padding: 6px; font-size: 10px;">{{ $data->due }}</td>
                </tr>
            @endif
            <tr>
                <td style="background-color: #ededed;border: 0; padding: 6px; font-size: 10px;">Vat</td>
                <td style="text-align: right;background-color: #ededed;font-weight: bold;border: 0; padding: 6px; font-size: 10px;">{{ $data->tax }}</td>
            </tr>
            <tr>
                <td style="border: 0; padding: 6px; font-size: 10px;">Discount</td>
                <td style="text-align: right;font-weight: bold;border: 0; padding: 6px; font-size: 10px;">{{ $data->discount }}</td>
            </tr>
            <tr>
                <td style="background-color: #ededed;border: 0; padding: 6px; font-size: 10px;">Shipping Cost</td>
                <td style="text-align: right;background-color: #ededed;font-weight: bold;border: 0; padding: 6px; font-size: 10px;">{{ $data->shippingCost }}</td>
            </tr>
            <tr>
                <td style="border: 0; padding: 6px; font-size: 10px;">Total Payment</td>
                <td style="text-align: right;font-weight: bold;border: 0; padding: 6px; font-size: 10px;">{{ $data->paid }}</td>
            </tr>
            <tr>
                <td style="background-color: #ededed;border: 0; padding: 6px; font-size: 10px;">Grand Total</td>
                <td style="text-align: right;background-color: #ededed;font-weight: bold;border: 0; padding: 6px; font-size: 10px;"> {{ $data->totalNetAmount }}</td>
            </tr>
        </table>
    </div>
</div>


<htmlpagefooter name="page-footer">
    <div align="right" style="font-size: 12px;">
        <i><b>{PAGENO} / {nbpg}</b></i>
    </div>
</htmlpagefooter>

</body>
</html>
