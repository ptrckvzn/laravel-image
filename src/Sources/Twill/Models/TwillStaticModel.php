<?php

namespace A17\Twill\Image\Sources\Twill\Models;

use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Model;

class TwillStaticModel extends Model
{
    use HasMedias;

    protected $fillable = [];

    public $mediasParams = [];

    public function makeMedia($args)
    {
        $src = $args['src'];

        $role = $args['role'];

        $crop = $args['crop'];

        $uuid = parse_url($src, PHP_URL_PATH);

        $size = $this->getInputSize($uuid);

        $width = $size[0];

        $height = $size[1];

        $ratio = $args['ratio'] ?? null;

        $cropData = $this->calcCrop($width, $height, $ratio);

        $media = \A17\Twill\Models\Media::make([
            'uuid' => $uuid,
            'filename' => basename($uuid),
            'width' => $width,
            'height' => $height,
            'alt_text' => $args['alt'] ?? null,
        ]);

        $data = [
                'role' => $role,
                'crop' => $crop,
            ] + $cropData;

        $pivot = $media->newPivot(
            $this,
            $data,
            config('twill.mediables_table', 'twill_mediables'),
            true,
        );

        $media->setRelation('pivot', $pivot);

        $this->medias->add($media);
    }

    private function getInputSize($uuid)
    {
        $file_path = implode('/', [
            rtrim(config('twill-image.static_local_path'), '/'),
            ltrim($uuid, '/'),
        ]);

        $size = getimagesize($file_path);

        return [$size[0], $size[1]];
    }

    private function calcCrop($inputWidth, $inputHeight, $outputRatio = null)
    {
        $inputImageAspectRatio =  $inputWidth / $inputHeight;
        $outputImageAspectRatio = isset($outputRatio) ? $outputRatio : $inputImageAspectRatio;

        $outputWidth = $inputWidth;
        $outputHeight = $inputHeight;

        if ($inputImageAspectRatio > $outputImageAspectRatio) {
            $outputWidth = $inputHeight * $outputImageAspectRatio;
        } elseif ($inputImageAspectRatio < $outputImageAspectRatio) {
            $outputHeight = $inputWidth / $outputImageAspectRatio;
        }

        return [
            'crop_x' => $outputWidth < $inputWidth ? ($inputWidth - $outputWidth) / 2 : 0,
            'crop_y' => $outputHeight < $inputHeight ? ($inputHeight - $outputHeight) / 2 : 0,
            'crop_w' => $outputWidth,
            'crop_h' => $outputHeight,
            'ratio' => $outputImageAspectRatio,
        ];
    }
}
