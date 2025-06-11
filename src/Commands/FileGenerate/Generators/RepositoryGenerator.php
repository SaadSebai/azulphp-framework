<?php

namespace Azulphp\Commands\FileGenerate\Generators;

use Azulphp\Commands\FileGenerate\FileGenerator;

class RepositoryGenerator extends FileGenerator
{

    public function handle(): void
    {
        $targetPath = "app/Repositories/{$this->args[0]}";

        $this->generate(
            stubPath: $this->stubPath('repository'),
            targetPath: $targetPath,
            replacements: [
                'namespace' => $this->getNamespace($this->args[0]),
                'class' => $this->getClass()
            ]
        );
    }
}