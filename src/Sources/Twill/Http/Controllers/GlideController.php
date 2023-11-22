<?php

namespace A17\LaravelImage\Sources\Twill\Http\Controllers;

use A17\LaravelImage\Sources\Twill\Services\TwillGlide;
use Illuminate\Foundation\Application;

class GlideController
{
    public function __invoke($path, Application $app)
    {
        return $app->make(TwillGlide::class)->render($path);
    }
}
