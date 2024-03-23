<?php

namespace App\Providers;

use App\Services\Ecommerce\WoocomCommunication;
use App\Services\Ecommerce\WoocomCommunicationService\WoocomCommunicationService;
use App\Support\DatabaseChannel;
use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // bind Woocommerce
        $this->app->bind(WoocomCommunication::class, function() {
            return new WoocomCommunicationService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Bind our Notification Database Channel override class
        $this->app->instance(IlluminateDatabaseChannel::class, new DatabaseChannel);

        $noOfRequests = 0;
        \DB::listen(function ($query) use (&$noOfRequests) {
            if (!App::environment('production')) {
                $noOfRequests++;
                \Log::debug("$noOfRequests - [time: $query->time] " . $query->sql . ' , ' . json_encode($query->bindings));
            }
        });
    }
}
