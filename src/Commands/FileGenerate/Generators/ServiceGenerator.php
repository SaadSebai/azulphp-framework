<?php

namespace Azulphp\Commands\FileGenerate\Generators;

use Azulphp\Commands\FileGenerate\FileGenerator;

class ServiceGenerator extends FileGenerator
{
    public function handle(): void
    {
        $file = $this->getInputFileName();

        $targetPath = "app/Services/$file";

        $this->generate(
            stubPath: $this->stubPath('service'),
            targetPath: $targetPath,
            replacements: [
                'namespace' => $this->getNamespace($file),
                'class' => $this->getFileNameWithoutPath()
            ]
        );
    }
}