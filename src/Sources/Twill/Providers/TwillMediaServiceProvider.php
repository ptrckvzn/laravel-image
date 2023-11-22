<?php

namespace A17\Twill\Image\Sources\Twill\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class TwillMediaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (config('twill-image.static_image_support')) {
            Relation::enforceMorphMap([
                'staticImages' => 'A17\Twill\Image\Sources\Twill\Models\TwillStaticModel',
            ]);
        }
    }

    public function register()
    {
        $this->app->register(TwillMediaRouteServiceProvider::class);
    }
}
