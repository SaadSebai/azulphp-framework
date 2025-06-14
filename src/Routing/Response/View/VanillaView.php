<?php

namespace Azulphp\Routing\Response\View;

class VanillaView implements ViewResponse
{
    public function __construct(
        protected string $path,
        protected array $attributes = [],
        protected ?string $layout = null
    )
    {
    }

    public function response(): void
    {
        extract($this->attributes);

        if ($this->layout)
        {
            ob_start();
            require base_path("views/{$this->path}.view.php");
            $slot = ob_get_clean();

            require base_path("views/layouts/$this->layout.php");
        }
        else
        {
            require base_path("views/{$this->path}.view.php");
        }
    }
}