<?php

namespace A17\LaravelImage\Sources\Glide\Http\Controllers;

use A17\LaravelImage\Sources\Glide\Services\Glide;
use Illuminate\Foundation\Application;

class GlideController
{
    public function __invoke($path, Application $app)
    {
        return $app->make(Glide::class)->render($path);
    }
}
