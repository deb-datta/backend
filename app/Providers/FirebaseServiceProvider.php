<?php

namespace App\Providers;

use Kreait\Firebase;
use FirebaseServiceAccount;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory as FirebaseFactory;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Firebase::class, function() {
            return (new FirebaseFactory())
                ->withServiceAccount(FirebaseServiceAccount::fromJsonFile(base_path('fb_config.json')))
                ->withDatabaseUri('https://uniliverproject-3e3c8.firebaseio.com/')
                ->create();
        });

        $this->app->alias(Firebase::class, 'firebase');
    }
}