<?php

namespace A17\Twill\Image\Sources\Twill\Providers;

use A17\Twill\Image\Sources\Twill\Http\Controllers\GlideController;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class TwillMediaRouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        parent::boot();

        if (config('twill-image.static_image_support')) {
            Relation::enforceMorphMap([
                'staticImages' => 'A17\Twill\Image\Sources\Twill\Models\TwillStaticModel',
            ]);
        }
    }

    /**
     * @param Router $router
     * @return void
     */
    public function map(Router $router)
    {
        if (config('twill-image.static_image_support')) {
            $basePath = config('twill-image.glide.base_path');

            $router->get("/$basePath/{path}", GlideController::class)
                ->where('path', '.*');
        }
    }
}
