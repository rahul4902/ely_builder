<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Support\CurrentCompany;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CurrentCompany::class);
        if ($this->app->environment('local', 'testing') && class_exists(\Laravel\Dusk\DuskServiceProvider::class)) {
            $this->app->register(\Laravel\Dusk\DuskServiceProvider::class);
        }
    }
}

if (!function_exists('elixir')) {
    function elixir($file, $buildDirectory = '')
    {
        return $file;
    }
}
