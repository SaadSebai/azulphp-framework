<?php

namespace Azulphp\Commands\FileGenerate\Generators;

use Azulphp\Commands\FileGenerate\FileGenerator;

class RequestGenerator extends FileGenerator
{
    public function handle(): void
    {
        $file = $this->getInputFileName();

        $targetPath = "app/Http/Requests/$file";

        $this->generate(
            stubPath: $this->stubPath('request'),
            targetPath: $targetPath,
            replacements: [
                'namespace' => $this->getNamespace($file),
                'class' => $this->getFileNameWithoutPath()
            ]
        );
    }
}