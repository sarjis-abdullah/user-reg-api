<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductStockExport implements FromView
{
    public $product;
    public function __construct($product)
    {
        $this->product = $product;
    }

    public function view(): View
    {
        return view('excel.report.stockReport', [
            'data' => json_encode($this->product)
        ]);
    }
}
