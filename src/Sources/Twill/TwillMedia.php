<?php

namespace A17\Twill\Image\Sources\Twill;

use A17\Twill\Image\Sources\Interfaces\MediaSource;
use A17\Twill\Image\Sources\Interfaces\Source;
use A17\Twill\Image\Sources\Twill\Services\TwillMediaSource;

class TwillMedia implements Source
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
