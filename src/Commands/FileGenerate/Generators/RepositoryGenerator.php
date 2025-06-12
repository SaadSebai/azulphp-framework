<?php

namespace Azulphp\Commands\FileGenerate\Generators;

use Azulphp\Commands\FileGenerate\FileGenerator;

class RepositoryGenerator extends FileGenerator
{

    public function handle(): void
    {
        $file = $this->getInputFileName();

        $targetPath = "app/Repositories/$file";

        $this->generate(
            stubPath: $this->stubPath('repository'),
            targetPath: $targetPath,
            replacements: [
                'namespace' => $this->getNamespace($file),
                'class' => $this->getFileNameWithoutPath()
            ]
        );
    }
}