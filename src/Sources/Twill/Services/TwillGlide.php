<?php

namespace A17\LaravelImage\Sources\Twill\Services;

use A17\Twill\Services\MediaLibrary\Glide;
use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class TwillGlide extends Glide
{
    public function __construct(Config $config, Application $app, Request $request)
    {
        $config->set('twill.glide.source', $config->get('laravel-image.glide.source'));
        $config->set('twill.glide.base_path', $config->get('laravel-image.glide.base_path'));

        parent::__construct($config, $app, $request);
    }
}
