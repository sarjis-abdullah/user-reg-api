<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SupplierWisePurchaseExport implements FromView
{
    public $purchases;
    public function __construct($purchases)
    {
        $this->purchases = $purchases;
    }

    public function view(): View
    {
        return view('excel.report.supplierWisePurchase', [
            'data' => $this->purchases
        ]);
    }
}
