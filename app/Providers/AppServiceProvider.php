<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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

    public function boot()
    {
        app('Dingo\Api\Transformer\Factory')->setAdapter(function () {
            $fractalManager = new \League\Fractal\Manager;
            $fractalManager->setSerializer(new \Api\Fractal\DataArraySerializer);
            return new \Dingo\Api\Transformer\Adapter\Fractal($fractalManager);
        });
    }
}
