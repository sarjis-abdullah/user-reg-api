<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductWiseSaleExport implements FromView
{
    public $products;
    public function __construct($products)
    {
        $this->products = $products;
    }

    public function view(): View
    {
        return view('excel.report.productWiseSale', [
            'data' => json_encode($this->products)
        ]);
    }
}
