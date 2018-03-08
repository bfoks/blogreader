<?php

namespace App\Providers;

use App\Platforms\AbstactClientsProvider;
use App\Platforms\ClientsProvider;
use App\Platforms\ProductionClientsProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment() !== 'local') {
            URL::forceScheme('https');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->instance(ClientsProvider::class, new ProductionClientsProvider());
    }
}
