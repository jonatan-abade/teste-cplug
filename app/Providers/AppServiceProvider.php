<?php

namespace App\Providers;

use App\Events\SaleFinalized;
use App\Listeners\UpdateInventory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $listen = [
        SaleFinalized::class => [
            UpdateInventory::class,
        ],
    ];

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
        //
    }
}
