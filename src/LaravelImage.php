<?php

namespace A17\LaravelImage;

use A17\LaravelImage\Models\Image;
use A17\LaravelImage\Sources\Interfaces\MediaSource;
use A17\LaravelImage\ViewModels\ImageViewModel;

class LaravelImage
{
    /**
     * @param object $object
     * @param string $role
     * @param null $media
     * @return Image
     */
    public function make(MediaSource $source): Image
    {
        return new Image($source);
    }

    /**
     * @param Image|array $data
     * @param array $overrides
     * @return string
     */
    public function render($data, $overrides = [])
    {
        $viewModel = new ImageViewModel($data, $overrides);

        return view('laravel-image::wrapper', $viewModel);
    }
}
