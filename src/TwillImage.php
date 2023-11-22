<?php

namespace A17\Twill\Image;

use A17\Twill\Image\Models\Image;
use A17\Twill\Image\Services\Interfaces\MediaSource;
use A17\Twill\Image\ViewModels\ImageViewModel;

class TwillImage
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

        return view('twill-image::wrapper', $viewModel);
    }
}
