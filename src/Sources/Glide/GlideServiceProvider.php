<?php

namespace A17\LaravelImage\Sources\Glide;

use A17\LaravelImage\Sources\Glide\Services\Glide;
use Illuminate\Support\ServiceProvider;

class GlideServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/laravel-image-glide.php',
            'laravel-image-glide',
        );
    }

    public function register()
    {
        $this->app->singleton('laravelImageGlide', function () {
            return $this->app->make(Glide::class);
        });

        $this->app->register(GlideRouteServiceProvider::class);
    }
}
