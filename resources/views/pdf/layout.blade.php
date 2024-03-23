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
            font-family: Verdana, Arial, Tahoma, Serif;
            font-size: 10px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 0.01em solid #d0cece;
            padding: 6px;
            text-align: center;
            font-size: 10px;
        }

        th {
            background-color: #ddd;
            font-weight: bold;
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
<htmlpageheader name="page-header">
    <div class="main">
        <div class="header">
            <img style="margin-top: 20px;max-width: 130px;max-height:30px" src="{{ \App\Services\Helpers\AppSettingHelper::logoUrl() }}">
            {{--<img style="margin-top: 20px;max-width: 130px;max-height:30px" src="{{ public_path('/logo/dark-logo.png') }}">--}}
            <p>{{ $branch }}</p>
        </div>
        <div class="table-title">
            @yield('title')
        </div>
        <div class="print-history">
            Print Date: {{ \Carbon\Carbon::now()->format('d M, Y, h:i:s A') }}
        </div>
    </div>
</htmlpageheader>

@yield('pdfContent')

<htmlpagefooter name="page-footer">
    <div align="right" style="font-size: 12px;">
        <i><b>{PAGENO} / {nbpg}</b></i>
    </div>
</htmlpagefooter>

</body>
</html>
