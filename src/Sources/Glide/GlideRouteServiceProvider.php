<?php

namespace A17\LaravelImage\Sources\Glide;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use A17\LaravelImage\Sources\Glide\Http\Controllers\GlideController;

class GlideRouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        parent::boot();
    }

    /**
     * @param Router $router
     * @return void
     */
    public function map(Router $router)
    {
        $router->get('/glide/{path}', GlideController::class)->where('path', '.*');
    }
}
