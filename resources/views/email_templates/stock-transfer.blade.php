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

    <tr>
        <td style="padding: 0px 56px 0">
            <table style="border-collapse: collapse; width: 100%; max-width: 600px; margin: 0 auto; background-color: #f1f1f1;">
                <tr>
                    <th style="padding: 10px; text-align: left; background-color: #e0e0e0; border: 1px solid #ccc;">Product</th>
                    <th style="padding: 10px; text-align: left; background-color: #e0e0e0; border: 1px solid #ccc;">Sku</th>
                    <th style="padding: 10px; text-align: left; background-color: #e0e0e0; border: 1px solid #ccc;">Quantity</th>
                    <th style="padding: 10px; text-align: left; background-color: #e0e0e0; border: 1px solid #ccc;">Total Amount</th>
                </tr>
                @foreach ($products as $product)
                    <tr>
                        <td style="padding: 10px; text-align: left; background-color: white; border: 1px solid #ccc;">{{ $product->product->name }}</td>
                        <td style="padding: 10px; text-align: left; background-color: white; border: 1px solid #ccc;">{{ $product->sku }}</td>
                        <td style="padding: 10px; text-align: left; background-color: white; border: 1px solid #ccc;">{{ $product->quantity }}</td>
                        <td style="padding: 10px; text-align: left; background-color: white; border: 1px solid #ccc;">{{ $product->totalAmount }}</td>
                    </tr>
                @endforeach
            </table>
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
                Go to Stock Expiration Report
            </a>
        </td>
    </tr>
@endsection
