@extends('email_templates.email-template')

@section('content')
    <tr>
        <td style="height: 40px; padding-top: 32px">
            <h1
                style="
                    font-style: normal;
                    font-weight: bold;
                    font-size: 24px;
                    line-height: 40px;
                    text-align: center;
                    color: #1d1e2d;
                "
            >
                {{ $header }}
            </h1>
        </td>
    </tr>
    <tr>
        <td style="padding: 32px 56px 0">
            <h1
                style="
                    font-style: normal;
                    font-weight: normal;
                    font-size: 16px;
                    line-height: 24px;
                    color: #1d1e2d;
                "
            >
                Hi, {{ $to_name }}
            </h1>

            <div
                style="
                    margin-top: 24px;
                    min-height: 40px;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    color: #1d1e2d;
                "
            >
                <p
                    style="font-size: 14px;
                    line-height: 18px;
                    text-align: justify;"
                >
                    {{ $description }}
                    <br>
                </p>
            </div>
        </td>
    </tr>
@endsection
