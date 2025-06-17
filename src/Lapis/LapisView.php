<?php

namespace Azulphp\Lapis;

class LapisView
{
    protected LapisCompiler $compiler;

    public function __construct()
    {
        $this->compiler = new LapisCompiler();
    }

    public function render(string $view, array $params = []): string
    {
        $viewFile = $this->getViewsPath() . "/$view.lapis.php";
        $cacheFile = $this->getCachePath() . "/". md5($view) . ".php";

        $template = file_get_contents($viewFile);
        $compiled = $this->compiler->compile($template);

        if (!file_exists(dirname($cacheFile))) {
            mkdir(dirname($cacheFile), 0755, true);
        }
        file_put_contents($cacheFile, $compiled);


        extract($params);
        ob_start();
        include $cacheFile;
        return ob_get_clean();
    }

    protected function getViewsPath(): string
    {
        return base_path('resources/views');
    }

    protected function getCachePath(): string
    {
        return base_path('resources/cache/views');
    }
}