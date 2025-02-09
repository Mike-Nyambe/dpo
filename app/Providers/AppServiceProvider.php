<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DpoService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the DpoService class
        $this->app->bind(DpoService::class, function ($app) {
            $order_id = "12345678"; // You can fetch this from a config file or database
            return new DpoService($order_id);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
