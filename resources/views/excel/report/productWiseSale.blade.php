<table>
    <tr>
        <th>Product Wise Sale Report</th>
        <th>{{ date('F m, Y h:i:s A') }}</th>
    </tr>
</table>
@if((request()->filled('startDate') && request()->filled('endDate')) || request()->filled('productId') || request()->filled('categoryId'))
    <table style="margin-bottom: 10px">
        <tr>
            <th colspan="2">
                @if(request()->filled('startDate') && request()->filled('endDate'))
                    Date: {{ date('d M, Y', strtotime(request()->get('startDate'))) .' to '. date('d M, Y', strtotime(request()->get('endDate'))) }}
                @endif
                @if(request()->filled('productId'))
                    @php
                        $productsIds = explode(',', request()->get('productId'));
                        $products = \App\Models\Product::query()->whereIn('id', $productsIds)->get(['name']);
                    @endphp
                    &nbsp;&nbsp;&nbsp; Product:
                    @foreach($products as $key => $product)
                        {{ $key > 0 ? ', ' : '' }} {{ ucfirst($product->name) }}
                    @endforeach
                @endif
                @if(request()->filled('categoryId'))
                    @php
                        $category = \App\Models\Category::query()->where('id', request()->get('categoryId'))->first(['name']);
                    @endphp
                    &nbsp;&nbsp;&nbsp; Category: {{ ucfirst($category->name) }}
                @endif
            </th>
        </tr>
    </table>
@endif
<table>
    <thead>
    <tr>
        <th>SL NO</th>
        <th>PRODUCT NAME</th>
        <th>UNIT NAME</th>
        <th>SKU</th>
        <th>SALES QUANTITY</th>
        <th>SALES AMOUNT</th>
        <th>RETURN AMOUNT</th>
        <th>NET SALES AMOUNT</th>
        <th>GROSS PROFIT</th>
    </tr>
    </thead>
    <tbody>
    @php
        $totalSaleOty = 0;
        $totalSalesAmount = 0;
        $totalReturnAmount = 0;
        $totalNetSalesAmount = 0;
        $totalGrossProfit = 0;
    @endphp
    @foreach(json_decode($data) as $key => $product)
        <tr>
            <td>{{ ($key+1) }}</td>
            <td>{{ $product->productName }}</td>
            <td>{{ $product->unitName }}</td>
            <td>{{ $product->sku }}</td>
            <td>{{ round($product->totalSaleQuantity, 2) }}</td>
            <td>{{ round($product->totalSaleAmount, 2) }}</td>
            <td>{{ round($product->totalReturnAmount, 2) }}</td>
            <td>{{ round(($product->totalSaleAmount - $product->totalReturnAmount), 2) }}</td>
            <td>{{ round($product->totalGrossProfitAmount - ($product->totalReturnProfitAmount - $product->totalReturnDiscountAmount), 2) }}</td>
        </tr>
        @php
            $totalSaleOty += round($product->totalSaleQuantity, 2);
            $totalSalesAmount += round($product->totalSaleAmount, 2);
            $totalReturnAmount += round($product->totalReturnAmount, 2);
            $totalNetSalesAmount += round(($product->totalSaleAmount - $product->totalReturnAmount), 2);
            $totalGrossProfit += round($product->totalGrossProfitAmount - ($product->totalReturnProfitAmount - $product->totalReturnDiscountAmount), 2);
        @endphp
    @endforeach
    <tr>
        <th>Total:</th>
        <th></th>
        <th></th>
        <th></th>
        <th>{{ $totalSaleOty }}</th>
        <th>{{ $totalSalesAmount }}</th>
        <th>{{ $totalReturnAmount }}</th>
        <th>{{ $totalNetSalesAmount }}</th>
        <th>{{ $totalGrossProfit }}</th>
    </tr>
    </tbody>
</table>
