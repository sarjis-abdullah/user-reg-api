<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ForceLogoutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'force:logout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forcefully logout users';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // DB::table('oauth_access_tokens')->delete();

        // $this->info('User sessions forcefully logged out.');
    }
}
