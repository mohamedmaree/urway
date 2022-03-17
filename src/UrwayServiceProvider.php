<?php

namespace maree\urway;

use Illuminate\Support\ServiceProvider;

class UrwayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__.'/config/urway.php' => config_path('urway.php'),
        ],'urway');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/urway.php', 'urway'
        );
    }
}
