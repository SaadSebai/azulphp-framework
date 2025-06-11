<?php

namespace Azulphp\Commands\FileGenerate\Generators;

use Azulphp\Commands\FileGenerate\FileGenerator;

class MiddlewareGenerator extends FileGenerator
{

    public function handle(): void
    {
        $targetPath = "app/Http/Middlewares/{$this->args[0]}";

        $this->generate(
            stubPath: $this->stubPath('middleware'),
            targetPath: $targetPath,
            replacements: [
                'namespace' => $this->getNamespace($this->args[0]),
                'class' => $this->getClass()
            ]
        );
    }
}