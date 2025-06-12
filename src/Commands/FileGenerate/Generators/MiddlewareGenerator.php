<?php

namespace Azulphp\Commands\FileGenerate\Generators;

use Azulphp\Commands\FileGenerate\FileGenerator;

class MiddlewareGenerator extends FileGenerator
{

    public function handle(): void
    {
        $targetPath = "app/Http/Middlewares/$this->fileName";

        $this->generate(
            stubPath: $this->stubPath('middleware'),
            targetPath: $targetPath,
            replacements: [
                'namespace' => $this->getNamespace($this->fileName),
                'class' => $this->getFileNameWithoutPath()
            ]
        );
    }
}