<?php

use Azulphp\Response\View\VanillaView;
use Azulphp\Response\View\View;
use Azulphp\Response\View\ViewResponse;

function view(string $path, array $attributes = [], ?string $layout = null, string $viewResponse = VanillaView::class): ViewResponse
{
    return View::make($path, $attributes, $layout, $viewResponse);
}