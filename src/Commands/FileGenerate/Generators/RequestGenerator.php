<?php

namespace Azulphp\Commands\FileGenerate\Generators;

use Azulphp\Commands\FileGenerate\FileGenerator;

class RequestGenerator extends FileGenerator
{
    public function handle(): void
    {
        $targetPath = "app/Http/Requests/{$this->args[0]}";

        $this->generate(
            stubPath: $this->stubPath('request'),
            targetPath: $targetPath,
            replacements: [
                'namespace' => $this->getNamespace($this->args[0]),
                'class' => $this->getClass()
            ]
        );
    }
}