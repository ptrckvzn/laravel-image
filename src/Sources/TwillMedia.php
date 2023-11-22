<?php

namespace A17\Twill\Image\Sources;

use A17\Twill\Image\Services\Interfaces\MediaSource;
use A17\Twill\Image\Services\Twill\TwillMediaSource;

class TwillMedia
{
    public static function make($object, $role, $media = null, $service = null): MediaSource
    {
        $source = new TwillMediaSource(
            $object,
            $role,
            $media,
            $service
        );

        return $source;
    }
}
