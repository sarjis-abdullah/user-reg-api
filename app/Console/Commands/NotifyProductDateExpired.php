<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Notifications\ProductExpired;
use App\Repositories\Contracts\BranchRepository;
use App\Repositories\Contracts\StockRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyProductDateExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:product-date-expired-notification {--day=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify for product date expiration';

    /**
     * @var StockRepository
     */
    protected $stockRepository;

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
     * @return mixed
     */
    public function handle()
    {
        $stockRepository = app(StockRepository::class);
        $day = $this->option('day');
        $startDate = Carbon::now()->toDateString();
        $endDate = Carbon::now()->addDays($day)->toDateString();

        $branches = app(BranchRepository::class)->getModel()->all();

        foreach ($branches as $branch) {
            $products = $stockRepository->getModel()
                ->whereDate('expiredDate', '<=', $endDate)
                ->whereDate('expiredDate', '>=', $startDate)
                ->where('branchId', '=', $branch->id)
                ->where('quantity', '>', 0)
                ->get();

            if(count($products)) {
                $this->notifyUsers($branch, $products, $day);
                $this->info('Stock expiration report sent to all the manager users of ' . $branch->name );
            }
        }

        $this->info('Products stock expiration list sent successfully!');
    }

    /**
     * @param Branch $branch
     * @param $products
     * @param $day
     * @return void
     */
    public function notifyUsers(Branch $branch, $products, $day)
    {
        $users = $branch->adminUserRoles->map(fn ($userRole) => $userRole->user && $userRole->user->isActive);

        $users->each(function ($user) use ($branch, $products, $day) {
            if ($user){
                $user->notify(new ProductExpired($branch, $products, $day));
            }
        });
    }

}
