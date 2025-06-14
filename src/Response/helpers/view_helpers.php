<?php

use Azulphp\Routing\Response\View\VanillaView;
use Azulphp\Routing\Response\View\View;
use Azulphp\Routing\Response\View\ViewResponse;

function view(string $path, array $attributes = [], ?string $layout = null, string $viewResponse = VanillaView::class): ViewResponse
{
    return View::make($path, $attributes, $layout, $viewResponse);
}