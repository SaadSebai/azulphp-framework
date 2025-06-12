<?php

namespace Azulphp\Commands\FileGenerate\Generators;

use Azulphp\Commands\FileGenerate\FileGenerator;

class ControllerGenerator extends FileGenerator
{
    public function handle(): void
    {
        $targetPath = "app/Http/Controllers/$this->fileName";

        $this->generate(
            stubPath: $this->stubPath('controller'),
            targetPath: $targetPath,
            replacements: [
                'namespace' => $this->getNamespace($this->fileName),
                'class' => $this->getFileNameWithoutPath()
            ]
        );
    }
}