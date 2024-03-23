<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesPersonReportExport implements FromView
{
    public $salesPerson;
    public function __construct($salesPerson)
    {
        $this->salesPerson = $salesPerson;
    }

    public function view(): View
    {
        return view('excel.report.salesPersonReport', [
            'data' => $this->salesPerson
        ]);
    }
}
