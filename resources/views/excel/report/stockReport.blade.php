<table>
    <tr>
        <th>Stock Report</th>
        <th>Print Date: {{ \Carbon\Carbon::now()->format('d M, Y, h:i:s A') }}</th>
    </tr>
</table>
<table>
    <thead>
    <tr>
        <th>SL NO</th>
        <th>PRODUCT NAME</th>
        <th>SKU</th>
        <th>EXPIRED DATE</th>
        <th>BRANCH</th>
        <th>CURRENT STOCK</th>
        <th>COST PRICE</th>
        <th>STOCK COST VALUE</th>
        <th>SALE PRICE</th>
        <th>STOCK SALE VALUE</th>
{{--        <th>SOLD UNIT</th>--}}
    </tr>
    </thead>
    <tbody>
    @php
        $totalCurrentStock = 0;
        $totalStockValue = 0;
        $totalStockSaleValue = 0;
//        $totalSoldQty = 0;
    @endphp
    @foreach(json_decode($data) as $key => $product)
        @foreach($product->stocks ?? [] as $sKey => $stocks)
            <tr>
                @if($sKey == 0)
                    <td rowspan="{{ count($product->stocks) }}">{{ ($key+1) }}</td>
                    <td rowspan="{{ count($product->stocks) }}">{{ $product->name }}</td>
                @endif
                <td>{{ $stocks->sku }}</td>
                <td>{{ $stocks->expiredDate ? date('d-m-Y', strtotime($stocks->expiredDate)) : '-' }}</td>
                <td>{{ isset($stocks->branch->name) ? $stocks->branch->name : '-' }}</td>
                <td>{{ round($stocks->quantity, 2) }}</td>
                <td>{{ round($stocks->unitCost, 2) }}</td>
                <td>{{ round($stocks->stockPrice, 2) }}</td>
                <td>{{ round($stocks->unitPrice, 2) }}</td>
                <td>{{ round($stocks->stockSalePrice, 2) }}</td>
{{--                <td>{{ round($stocks->totalSoldQuantity, 2) }}</td>--}}
            </tr>
            @php
                $totalCurrentStock += round($stocks->quantity, 2);
                $totalStockValue += round($stocks->stockPrice, 2);
                $totalStockSaleValue += round($stocks->stockSalePrice, 2);
//                $totalSoldQty += round($stocks->totalSoldQuantity, 2);
            @endphp
        @endforeach
    @endforeach
    <tr>
        <th>Total:</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th>{{ $totalCurrentStock }}</th>
        <th></th>
        <th>{{ $totalStockValue }}</th>
        <th></th>
        <th>{{ $totalStockSaleValue }}</th>
{{--        <th>{{ $totalSoldQty }}</th>--}}
    </tr>


    </tbody>
</table>
