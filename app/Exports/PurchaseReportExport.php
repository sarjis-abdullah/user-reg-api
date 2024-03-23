<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchaseReportExport implements FromView
{
    public $purchase;
    public function __construct($purchase)
    {
        $this->purchase = $purchase;
    }

    public function view(): View
    {
        return view('excel.report.purchase', [
            'data' => $this->purchase
        ]);
    }
}
