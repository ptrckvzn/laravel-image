<?php

namespace A17\LaravelImage\Services\Interfaces;

interface ImageColumns
{
    public static function shouldInstantiateService(): bool;

    public function sizes($sizes): string;

    public function mediaQuery($args): string;
}
