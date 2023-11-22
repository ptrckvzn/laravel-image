<?php

namespace A17\LaravelImage\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelImage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-image';
    }
}
