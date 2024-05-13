<?php

namespace App\Providers;


use App\Events\UserRegistrationCompletedEvent;
use App\Listeners\UserRegistrationCompletedListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserRegistrationCompletedEvent::class => [
            UserRegistrationCompletedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
