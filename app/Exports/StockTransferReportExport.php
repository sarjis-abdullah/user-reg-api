<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StockTransferReportExport implements FromView
{
    public $stockTransfer;
    public function __construct($stockTransfer)
    {
        $this->stockTransfer = $stockTransfer;
    }

    public function view(): View
    {
        return view('excel.report.stockTransferReport', [
            'data' => json_encode($this->stockTransfer)
        ]);
    }
}
