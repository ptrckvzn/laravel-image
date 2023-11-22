<?php

namespace A17\Twill\Image\Sources\Twill;

use A17\Twill\Image\Exceptions\ImageException;
use A17\Twill\Image\Sources\Interfaces\MediaSource;
use A17\Twill\Image\Sources\Interfaces\Source;
use A17\Twill\Image\Sources\Twill\Models\TwillStaticModel;

class TwillStaticMedia implements Source
{
    public static $role = 'static-image';

    public static function make($args): MediaSource
    {
        $object = self::makeFromSrc($args);

        return TwillMedia::make($object, self::$role, null, \A17\Twill\Image\Sources\Glide::class);
    }

    private static function makeFromSrc($args)
    {
        $model = TwillStaticModel::make();

        $role = self::$role;

        $preset = self::getPresetObject($args['preset'] ?? null);

        $files = $args['files'] ?? $args['file'] ?? null;

        $ratios = $args['ratios'] ?? $args['ratio'] ?? null;

        $crop = $preset['crop'] ?? 'default';

        $model->makeMedia([
            'src' => self::getFile($files, $crop),
            'ratio' => self::getRatio($ratios, $crop),
            'role' => $role,
            'crop' => $crop,
            'alt' => $args['alt'] ?? null,
        ]);

        if (!empty($preset['sources']) && $sources = $preset['sources']) {
            foreach ($sources as $source) {
                $model->makeMedia([
                    'src' => self::getFile($files, $source['crop']),
                    'ratio' => self::getRatio($ratios, $source['crop']),
                    'role' => $role,
                    'crop' => $source['crop'],
                ]);
            }
        }

        return $model;
    }

    private static function getPresetObject($preset)
    {
        if (is_array($preset)) {
            return $preset;
        } elseif (config()->has("twill-image.presets.$preset")) {
            return config("twill-image.presets.$preset");
        } else {
            return [];
        }
    }

    private static function getFile($files, $crop)
    {
        if (is_array($files) && isset($files[$crop])) {
            return $files[$crop];
        } elseif (is_string($files)) {
            return $files;
        } else {
            throw new ImageException("Invalid file(s) value in arguments.");
        }
    }

    private static function getRatio($ratios, $crop)
    {
        if (is_array($ratios) && isset($ratios[$crop])) {
            return $ratios[$crop];
        } elseif (is_numeric($ratios)) {
            return $ratios;
        } else {
            return null;
        }
    }
}
