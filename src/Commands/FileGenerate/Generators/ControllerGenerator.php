<?php

namespace Azulphp\Commands\FileGenerate\Generators;

use Azulphp\Commands\FileGenerate\FileGenerator;

class ControllerGenerator extends FileGenerator
{
    public function handle(): void
    {
        $targetPath = "app/Http/Controllers/{$this->args[0]}";

        $this->generate(
            stubPath: $this->stubPath('controller'),
            targetPath: $targetPath,
            replacements: [
                'namespace' => $this->getNamespace($this->args[0]),
                'class' => $this->getClass()
            ]
        );
    }
}