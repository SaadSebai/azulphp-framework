<?php

namespace Azulphp\Commands\FileGenerate\Generators;

use Azulphp\Commands\FileGenerate\FileGenerator;

class ApiResponseGenerator extends FileGenerator
{
    public function handle(): void
    {
        $targetPath = "app/Http/ApiResponses/{$this->args[0]}";

        $this->generate(
            stubPath: $this->stubPath('api-response'),
            targetPath: $targetPath,
            replacements: [
                'namespace' => $this->getNamespace($this->args[0]),
                'class' => $this->getClass()
            ]
        );
    }
}