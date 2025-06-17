<?php

use Azulphp\Routing\Response\View\LapisResponse;
use Azulphp\Routing\Response\View\View;
use Azulphp\Routing\Response\View\ViewResponse;

function view(string $path, array $params = [], ?string $layout = null, string $viewResponse = LapisResponse::class): ViewResponse
{
    return View::make($path, $params, $layout, $viewResponse);
}