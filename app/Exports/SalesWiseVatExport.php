<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesWiseVatExport implements FromView
{
    public $orders;
    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function view(): View
    {
        return view('excel.vat.salesWiseVatReport', [
            'orders' => json_encode($this->orders)
        ]);
    }
}
