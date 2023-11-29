<?php

namespace A17\LaravelImage\Sources\Glide;

use A17\LaravelImage\Exceptions\ImageException;
use A17\LaravelImage\Sources\Interfaces\MediaSource;

class GlideMediaSource implements MediaSource
{
    protected const AUTO_WIDTHS_RATIO = 2.5;

    protected const DEFAULT_WIDTH = 1000;

    protected const FORMAT_WEBP = 'webp';

    protected $model;

    protected $role;

    protected $media;

    protected $crop;

    protected $width;

    protected $srcSetWidths;

    protected $height;

    protected $imageArray;

    protected $path;

    protected $filePath;

    public function __construct($path)
    {
        $this->path = $path;
        $this->filePath = config('laravel-image-glide.source') . '/' . $path;
    }

    public function generate($crop = null, $width = null, $height = null, $srcSetWidths = []): object
    {
        $this->setCrop($crop);
        $this->setImageArray();
        $this->setWidth($width);
        $this->setHeight($height);
        $this->srcSetWidths = $srcSetWidths;

        return $this;
    }

    /**
     * Set model crop
     *
     * @param null|string $crop
     * @return void
     */
    protected function setCrop($crop)
    {
        if (isset($crop) && is_string($crop)) {
            $this->crop = $crop;
            return;
        }

        $this->crop = 'default';
    }

    protected function setWidth($width)
    {
        if (isset($width)) {
            $this->width = $width;
        } else {
            $this->width = min($this->imageArray['width'], self::DEFAULT_WIDTH);
        }
    }

    protected function setHeight($height)
    {
        $this->height = $height ?? null;
    }

    protected function setImageArray()
    {
        $imagesize = getimagesize($this->filePath);

        list($width, $height) = $imagesize;

        if (isset($width) && isset($height)) {
            $this->imageArray = [
                'width' => $width,
                'height' => $height,
                'mime' => $imagesize['mime'],
            ];
        }

        if (empty($this->imageArray)) {
            throw new ImageException(
                "No media was found for role '{$this->role}' and crop '{$this->crop}'",
                1,
            );
        }
    }

    protected function params($width, $height = null, $format = null)
    {
        $args = [
            'w' => $width,
        ];

        if (isset($height)) {
            $args['h'] = $height;
            $args['fit'] = 'crop';
        }

        if (isset($format)) {
            $args['fm'] = $format;
        }

        return $args;
    }

    protected function calcHeightFromWidth($width)
    {
        $height
            = isset($this->height)
            ? $width * $this->height / $this->width
            : $width * $this->imageArray['height'] / $this->imageArray['width'];

        return (int) $height;
    }

    private function getUrl($path, $params)
    {
        return app('laravelImageGlide')->getUrl($path, $params);
    }

    public function src()
    {
        return $this->getSrc($this->width, $this->height);
    }

    public function srcWebp()
    {
        return $this->getSrc($this->width, $this->height, self::FORMAT_WEBP);
    }

    protected function getSrc($width, $height, $format = null)
    {
        $params = $this->params($width, $height, $format);
        return $this->getUrl($this->path, $params);
    }

    public function lqipBase64()
    {
        return base64_encode(file_get_contents($this->filePath));
    }

    public function srcSet()
    {
        return $this->getSrcset();
    }

    public function srcSetWebp()
    {
        return $this->getSrcSet(self::FORMAT_WEBP);
    }

    protected function getSrcSet($format = null)
    {
        $range = !empty($this->srcSetWidths) ? collect($this->srcSetWidths) : collect($this->widthRange());

        return $range
            ->map(function ($width) use ($format) {
                return sprintf(
                    "%s %sw",
                    $this->getSrc(
                        $width,
                        isset($this->height) ? $this->calcHeightFromWidth($width) : null,
                        $format
                    ),
                    $width
                );
            })
            ->join(', ');
    }

    protected function widthRange()
    {
        $baseWidth = $this->width;

        // weird science 🥸
        $range = array_merge(
            range(min(250, $baseWidth), 1250, 250),
            range(1500, 10000, 500),
        );

        return array_filter($range, function ($width) use ($baseWidth) {
            return $width <= $baseWidth * self::AUTO_WIDTHS_RATIO;
        });
    }

    public function width()
    {
        return $this->width;
    }

    public function height()
    {
        return isset($this->height) ? $this->height : $this->calcHeightFromWidth($this->width);
    }

    public function aspectRatio(): string
    {
        $width = $this->width;

        $height
            = $this->height
            ?? $width
            * $this->imageArray['height']
            / $this->imageArray['width'];

        return (float) ($height / $width);
    }

    /**
     * Provide the text description of the image
     *
     * @return string
     */
    public function alt(): string
    {
        return 'Alt';
    }

    public function caption(): string
    {
        return 'Caption';
    }

    public function extension(): string
    {
        return pathinfo($this->filePath, PATHINFO_EXTENSION);
    }

    public function ratio(): string
    {
        return 1;
    }

    public function toArray()
    {
        return array_merge([
            "alt" => $this->alt(),
            "aspectRatio" => $this->aspectRatio(),
            "caption" => $this->caption(),
            "crop" => $this->crop,
            "extension" => $this->extension(),
            "height" => $this->height(),
            "lqipBase64" => $this->lqipBase64(),
            "ratio" => $this->ratio(),
            "src" => $this->src(),
            "srcSet" => $this->srcSet(),
            "width" => $this->width(),
        ], (config('laravel-image.webp_support') ? [
            "srcWebp" => $this->srcWebp(),
            "srcSetWebp" => $this->srcSetWebp(),
        ] : []));
    }
}
