<?php

namespace App\Providers;

use App\Filesystem\Adapter\Ftp;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        \Storage::extend('ftp', function ($app, $config) {
            return new Filesystem(new Ftp($config));
        });
    }
}
