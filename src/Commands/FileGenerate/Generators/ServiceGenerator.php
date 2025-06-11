<?php

namespace Azulphp\Commands\FileGenerate\Generators;

use Azulphp\Commands\FileGenerate\FileGenerator;

class ServiceGenerator extends FileGenerator
{
    public function handle(): void
    {
        $targetPath = "app/Services/{$this->args[0]}";

        $this->generate(
            stubPath: $this->stubPath('service'),
            targetPath: $targetPath,
            replacements: [
                'namespace' => $this->getNamespace($this->args[0]),
                'class' => $this->getClass()
            ]
        );
    }
}