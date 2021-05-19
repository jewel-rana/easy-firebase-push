<?php

namespace Rajtika\Firebase;

use Illuminate\Support\ServiceProvider;
use Rajtika\Firebase\Services\Firebase;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/firebase.php' =>  config_path('firebase.php'),
        ], 'config');
        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('firebase', function () {
            return new Firebase();
        });
    }
}
