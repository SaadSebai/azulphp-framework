<?php

namespace Azulphp\Routing\Response\View;

class View
{
    public function __construct()
    {
    }

    public static function make(string $path, array $params = [], ?string $layout = null, string $viewResponse = LapisResponse::class): ViewResponse
    {
        return new $viewResponse($path, $params, $layout);
    }
}