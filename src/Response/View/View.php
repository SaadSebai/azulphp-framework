<?php

namespace Azulphp\Response\View;

class View
{
    public static function make(string $path, array $attributes = [], ?string $layout = null, string $viewResponse = VanillaView::class): ViewResponse
    {
        return new $viewResponse($path, $attributes, $layout);
    }
}