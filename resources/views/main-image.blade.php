@include('laravel-image::picture', [
    'fallback' => $mainSrc,
    'attributes' => 'data-main-image',
    'sources' => $mainSources ?? [],
    'style' => $mainStyle,
])
<noscript>
    @include('laravel-image::picture', [
        'fallback' => $mainSrc,
        'attributes' => 'data-main-image',
        'shouldLoad' => true,
        'sources' => $mainSources ?? [],
        'style' => $mainNoscriptStyle,
    ])
</noscript>
