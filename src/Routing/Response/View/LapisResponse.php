<?php

namespace Azulphp\Routing\Response\View;

use Azulphp\Lapis\LapisView;

class LapisResponse implements ViewResponse
{
    protected LapisView $view;

    public function __construct(
        protected string $path,
        protected array $params = [],
        protected ?string $layout = null
    )
    {
        $this->view = new LapisView();
    }

    public function response(): string
    {
        return $this->view->render($this->path, $this->params);
    }
}