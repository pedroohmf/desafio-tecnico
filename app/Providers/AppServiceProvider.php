<?php

namespace App\Providers;

use App\Services\BuscarMoedas;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BuscarMoedas::class, function ($app) {
            return new BuscarMoedas();
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
