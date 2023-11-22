<?php

namespace A17\Twill\Image\Models;

use A17\Twill\Image\Exceptions\ImageException;
use A17\Twill\Image\Facades\TwillImage;
use A17\Twill\Image\Services\ImageColumns;
use A17\Twill\Image\Sources\Interfaces\MediaSource;
use Illuminate\Contracts\Support\Arrayable;

class Image implements Arrayable
{
    /**
     * @var string The media crop
     */
    protected $crop;

    /**
     * @var int The media width
     */
    protected $width;

    /**
     * @var int The media height
     */
    protected $height;

    /**
     * @var array The media sources
     */
    protected $sources = [];

    /**
     * @var string Sizes attributes
     */
    protected $sizes;

    /**
     * @var int[] Widths list used to generate the srcset attribute
     */
    protected $srcSetWidths = [];

    /**
     * @param MediaSource $object
     * @param string $role
     */
    public function __construct(MediaSource $object)
    {
        $this->mediaSource = $object;

        $columnsServiceClass = config('twill-image.columns_class', ImageColumns::class);

        if ($columnsServiceClass::shouldInstantiateService()) {
            $this->columnsService = new $columnsServiceClass();
        }
    }

    /**
     * Pick a preset from the configuration file or pass an array with the image configuration
     *
     * @param array|string $preset
     * @return $this
     */
    public function preset($preset)
    {
        if (is_array($preset)) {
            $this->applyPreset($preset);
        } elseif (config()->has("twill-image.presets.$preset")) {
            $this->applyPreset(config("twill-image.presets.$preset"));
        } else {
            throw new ImageException("Invalid preset value. Preset must be an array or a string corresponding to an image preset key in the configuration file.");
        }

        return $this;
    }

    public function columns($columns)
    {
        if (!isset($this->columnsService)) {
            return;
        }

        $this->sizes = $this->columnsService->sizes($columns);
    }

    protected function mediaQueryColumns($args)
    {
        if (!isset($this->columnsService)) {
            return null;
        }

        return $this->columnsService->mediaQuery($args);
    }

    /**
     * Set the list of srcset width to generate
     *
     * @param int[] $widths
     * @return $this
     */
    public function srcSetWidths($widths)
    {
        $this->srcSetWidths = $widths;
    }

    /**
     * Set the crop of the media to use
     *
     * @param string $crop
     * @return $this
     */
    public function crop($crop)
    {
        $this->crop = $crop;

        return $this;
    }

    /**
     * Set a fixed with or max-width
     *
     * @param int $width
     * @return $this
     */
    public function width($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Set a fixed height
     *
     * @param int $height
     * @return $this
     */
    public function height($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Set the image sizes attributes
     *
     * @param string $sizes
     * @return $this
     */
    public function sizes($sizes)
    {
        $this->sizes = $sizes;

        return $this;
    }

    /**
     * Set alternative sources for the media.
     *
     * @param array $sources
     * @return $this
     */
    public function sources($sources = [])
    {
        $this->sources = $sources;

        return $this;
    }

    /**
     * Set alternative sources for the media.
     *
     * @param array $sources
     * @return $this
     */
    public function generateSources()
    {
        $sources = [];

        foreach ($this->sources ?? [] as $source) {
            if (!isset($source['media_query']) && !isset($source['mediaQuery']) && !isset($source['columns'])) {
                throw new ImageException("Media query is mandatory in sources.");
            }

            if (!isset($source['crop'])) {
                throw new ImageException("Crop name is mandatory in sources.");
            }

            $sources[] = [
                "mediaQuery" => isset($source['columns'])
                    ? $this->mediaQueryColumns($source['columns'])
                    : $source['media_query'] ?? $source['mediaQuery'],
                "image" => $this->mediaSource->generate(
                    $source['crop'],
                    $source['width'] ?? null,
                    $source['height'] ?? null,
                    $source['srcSetWidths'] ?? [],
                )->toArray()
            ];
        }

        return $sources;
    }

    /**
     * Call the Facade render method to output the view
     *
     * @return void
     */
    public function render($overrides = [])
    {
        return TwillImage::render($this, $overrides);
    }

    public function toArray()
    {
        $arr = [
            "image" => $this->mediaSource->generate(
                $this->crop,
                $this->width,
                $this->height,
                $this->srcSetWidths
            )->toArray(),
            "sizes" => $this->sizes,
            "sources" => $this->generateSources(),
        ];

        return array_filter($arr);
    }

    protected function applyPreset($preset)
    {
        if (!isset($preset)) {
            return;
        }

        if (isset($preset['crop'])) {
            $this->crop($preset['crop']);
        }

        if (isset($preset['width'])) {
            $this->width($preset['width']);
        }

        if (isset($preset['height'])) {
            $this->height($preset['height']);
        }

        if (isset($preset['sizes'])) {
            $this->sizes($preset['sizes']);
        }

        if (isset($preset['columns'])) {
            $this->columns($preset['columns']);
        }

        if (isset($preset['sources'])) {
            $this->sources($preset['sources']);
        }

        if (isset($preset['srcSetWidths'])) {
            $this->srcSetWidths($preset['srcSetWidths']);
        }
    }
}
