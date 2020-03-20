<?php

namespace Voidpointers\Etsy\Provider;

use Illuminate\Support\ServiceProvider;
use Voidpointers\Etsy\Server;

class LumenServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('etsy', function ($app) {
            return new Server($app['config']['etsy']);
        });
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['etsy'];
    }

    public function boot()
    {
        $this->app->configure('etsy');
        $path = realpath(__DIR__ . '/../../config/etsy.php');
        $this->mergeConfigFrom($path, 'etsy');
    }
}
