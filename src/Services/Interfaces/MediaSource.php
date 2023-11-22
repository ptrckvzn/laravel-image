<?php

namespace A17\Twill\Image\Services\Interfaces;

use Illuminate\Contracts\Support\Arrayable;

interface MediaSource extends Arrayable
{
    public function generate($crop = null, $width = null, $height = null, $srcSetWidths = []): object;
    public function src();
    public function srcWebp();
    public function lqipBase64();
    public function srcSet();
    public function srcSetWebp();
    public function width();
    public function height();
    public function aspectRatio(): string;
    public function alt(): string;
    public function caption(): string;
    public function extension(): string;
    public function ratio(): string;
}
