<?php

namespace A17\LaravelImage\Sources\Glide\Services;

use Illuminate\Support\Str;
use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureFactory;
use League\Glide\Urls\UrlBuilderFactory;

class Glide
{
    protected $app;

    protected $config;

    protected $request;

    protected $server;

    public function __construct(Config $config, Application $app, Request $request)
    {
        $this->app = $app;
        $this->config = $config;
        $this->request = $request;

        $baseUrlHost = $this->config->get(
            'laravel-image-glide.base_url',
            $this->request->getScheme() . '://' . str_replace(
                ['http://', 'https://'],
                '',
                $this->config->get('app.url')
            )
        );

        $baseUrl = implode('/', [
            rtrim($baseUrlHost, '/'),
            ltrim($this->config->get('laravel-image-glide.base_path'), '/'),
        ]);

        if (!empty($baseUrlHost) && !Str::startsWith($baseUrl, ['http://', 'https://'])) {
            $baseUrl = $this->request->getScheme() . '://' . $baseUrl;
        }


        $this->server = ServerFactory::create([
            'response' => new LaravelResponseFactory($this->request),
            'source' => $this->config->get('laravel-image-glide.source'),
            'source_path_prefix' => $this->config->get('laravel-image-glide.source_path_prefix'),
            'cache' => $this->config->get('laravel-image-glide.cache'),
            'cache_path_prefix' => $this->config->get('laravel-image-glide.cache_path_prefix'),
            'base_url' => $baseUrl,
            'driver' => $this->config->get('laravel-image-glide.driver')
        ]);

        $this->urlBuilder = UrlBuilderFactory::create(
            $baseUrl,
            $this->config->get('laravel-image-glide.use_signed_urls') ? $this->config->get('laravel-image-glide.sign_key') : null
        );
    }

    public function render(string $path)
    {
        if ($this->config->get('laravel-image-glide.use_signed_urls')) {
            SignatureFactory::create($this->config->get('laravel-image-glide.sign_key'))->validateRequest($this->config->get('laravel-image-glide.base_path') . '/' . $path, $this->request->all());
        }

        return $this->server->getImageResponse($path, $this->request->all());
    }

    /**
     * @param string $id
     * @return string
     */
    public function getUrl($id, array $params = [])
    {
        $defaultParams = config('laravel-image-glide.default_params');

        return $this->getOriginalMediaUrl($id) ??
            $this->urlBuilder->getUrl($id, array_replace($defaultParams, $params));
    }

    /**
     * @param string $id
     * @return string
     */
    private function getOriginalMediaUrl($id)
    {
        $originalMediaForExtensions = $this->config->get('laravel-image-glide.original_media_for_extensions');
        $addParamsToSvgs = $this->config->get('laravel-image-glide.add_params_to_svgs', false);

        if ((Str::endsWith($id, '.svg') && $addParamsToSvgs) || !Str::endsWith($id, $originalMediaForExtensions)) {
            return null;
        }

        return Storage::url($id);
    }
}
