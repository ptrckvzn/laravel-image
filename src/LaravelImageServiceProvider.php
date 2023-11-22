<?php

namespace A17\LaravelImage;

use A17\LaravelImage\Sources\Twill\TwillMediaRouteServiceProvider;
use Illuminate\Support\ServiceProvider;

class LaravelImageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('laravel-image', function ($app) {
            return $app->make('A17\LaravelImage\LaravelImage');
        });

        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-image.php',
            'laravel-image',
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-image');
        $this->publishes(
            [
                __DIR__ . '/../config/laravel-image.php' => config_path(
                    'laravel-image.php',
                ),
            ],
            'config',
        );
        $this->publishes(
            [
                __DIR__ . '/../dist/laravel-image.js' => public_path(
                    'laravel-image.js',
                ),
            ],
            'js',
        );
    }
}
