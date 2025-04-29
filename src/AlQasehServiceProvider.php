<?php

namespace Osama\AlQaseh;

use Illuminate\Support\ServiceProvider;

class AlQasehServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AlQaseh::class, function ($app) {
            return new AlQaseh(
                config('alqaseh.api_key'),
                config('alqaseh.merchant_id'),
                config('alqaseh.base_url'),
                config('alqaseh.sandbox', true)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/alqaseh.php' => config_path('alqaseh.php'),
        ], 'config');
    }
}