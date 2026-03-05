<?php

namespace App\Providers;

use App\Models\FirstTimer;
use App\Observers\FirstTimerObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FirstTimer::observe(FirstTimerObserver::class);
    }
}
