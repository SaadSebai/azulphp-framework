<?php

namespace Azulphp\Commands\FileGenerate\Generators;

use Azulphp\Commands\FileGenerate\FileGenerator;
use Azulphp\Commands\FileGenerate\WithProperties;

/**
 * Create a model with the giving properties.
 */
class DtoGenerator extends FileGenerator
{
    use WithProperties;

    /**
     * Incoming console properties.
     *
     * @var array
     */
    protected array $properties;

    public function handle(): void
    {
        $targetPath = "app/Http/Dtos/$this->fileName";
        $this->properties = $this->getProperties();
        $imports = $this->imports('App\\Http\\Dtos');
        $contant = $this->contant();

        $this->generate(
            stubPath: $this->stubPath('dto'),
            targetPath: $targetPath,
            replacements: [
                'namespace' => $this->getNamespace($this->fileName),
                'class' => $this->getFileNameWithoutPath(),
                'contant' => $contant,
                'imports' => $imports,
            ]
        );
    }

    /**
     * Build the stub contant.
     *
     * @return string
     */
    protected function contant(): string
    {
        $contant = $this->printConstructor($this->properties);
        $contant .= $this->printMutators($this->properties);

        return $contant;
    }
}