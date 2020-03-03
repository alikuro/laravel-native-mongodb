<?php

namespace Alikuro\Mongodb;

use Illuminate\Support\ServiceProvider;
use Alikuro\Mongodb\Manager;

class MongodbServiceProvider extends ServiceProvider {
    /**
     * Bootstrap the application events.
     */
    public function boot() {
        // . . .
    }

    /**
     * Register the service provider.
     */
    public function register() {
        $this->app->singleton(Manager::class, function( $app ) {
            return new Manager( config('database.connections.mongodb') );
        });
    }
}

