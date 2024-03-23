<?php

namespace App\Console\Commands;

use App\Services\Reports\Dashboard;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class StoreDailySummaryHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:daily_summary_report_history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store daily summary report history AT 12:00 AM';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        // Dashboard::storeDailySummary();

        // echo 'Daily summary report store done!';
    }
}
