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
                <div style="display: flex;
                    flex-direction: row;
                    justify-content: flex-start;"
                >
                    <div style="font-size: 14px; font-weight: bold; line-height: 24px; text-align: justify; width: 70px;">
                        Product
                    </div>
                    <div style="font-size: 14px; line-height: 24px; text-align: justify;">
                        : {{ $product_name }}
                    </div>
                </div>

                <div style="display: flex;
                    flex-direction: row;
                    justify-content: flex-start;"
                >
                    <div style="font-size: 14px; font-weight: bold; line-height: 24px; text-align: justify; width: 70px;">
                        Quantity
                    </div>
                    <div style="font-size: 14px; line-height: 24px; text-align: justify;">
                        : {{ $quantity }}
                    </div>
                </div>

                <div style="display: flex;
                    flex-direction: row;
                    justify-content: flex-start;"
                >
                    <div style="font-size: 14px; font-weight: bold; line-height: 24px; text-align: justify; width: 70px;">
                        Branch
                    </div>
                    <div style="font-size: 14px; line-height: 24px; text-align: justify;">
                        : {{ $branch_name }}
                    </div>
                </div>
            </div>
        </td>
    </tr>

    <tr>
        <td style="padding: 32px 56px 42px 56px; text-align: center;">
            <a
                href="{{ $frontend_url }}"
                target="_blank"
                style="
                    padding: 10px 16px;
                    font-size: 14px;
                    line-height: 16px;
                    background: transparent;
                    border: 1px solid #0363ed;
                    color: #0363ed;
                    letter-spacing: 1.25px;
                    box-sizing: border-box;
                    border-radius: 4px;
                    font-weight: bold;
                    cursor: pointer;
                    text-decoration: none;
                "
            >
                Go to Stock
            </a>
        </td>
    </tr>
@endsection
