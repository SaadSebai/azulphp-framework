<?php

namespace Azulphp\Commands\FileGenerate\Generators;

use Azulphp\Commands\FileGenerate\FileGenerator;
use Azulphp\Commands\Support\Output;
use Azulphp\Helpers\Str;

/**
 * Create a model with the giving properties.
 */
class ModelGenerator extends FileGenerator
{
    /**
     * Incoming console properties.
     *
     * @var array
     */
    protected array $properties;

    public function handle(): void
    {
        $targetPath = "app/Models/$this->fileName";
        $this->properties = $this->getProperties();
        $imports = $this->imports();
        $contant = $this->contant();

        $this->generate(
            stubPath: $this->stubPath('model'),
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
        $contant = $this->printProperties($this->properties);
        $contant .= $this->printMutators($this->properties);

        return $contant;
    }

    /**
     * Build the sub imports.
     *
     * @return string
     */
    protected function imports(): string
    {
        $imports = '';

        foreach ($this->properties as $key => $property)
        {
            if (str_contains($property['type'], '\\'))
            {
                $this->properties[$key]['type'] = Str::afterLast($property['type'], '\\');

                if ($property['type'] !== "App\\Models\\{$this->properties[$key]['type']}")
                    $imports .=  PHP_EOL . "use {$property['type']};";
            }
        }

        return $imports . PHP_EOL;
    }

    /**
     * Read the properties received form the console.
     *
     * @return array
     */
    protected function getProperties(): array
    {
        $isWriting = true;
        $inputs = [];

        while ($isWriting)
        {
            $this->output->secondary('Enter the property name: (press ENTER if you want to finish)');
            $prop = $this->input->line();

            if ($prop !== '')
            {
                $type = $this->input->requireLine(
                    before: fn() => $this->output->secondary('Enter the property type: (*)'),
                    validation: fn ($line) => valideVarType($line)
                );

                $inputs[] = compact('prop', 'type');
            }
            else
            {
                $isWriting = false;
            }
        }

        return $inputs;
    }

    /**
     * Create the properties skeleton that will be printed in the stub.
     *
     * @param  array  $properties
     * @return string
     */
    protected function printProperties(array $properties): string
    {
        $result = '';
        foreach ($properties as $property)
        {
            $result .= "\tprotected {$property['type']} \${$property['prop']};" . PHP_EOL;
        }

        return $result;
    }

    /**
     * Create the mutator skeleton that will be printed in the stub.
     *
     * @param  array  $properties
     * @return string
     */
    protected function printMutators(array $properties): string
    {
        $result = '';
        foreach ($properties as $property)
        {
            $camelProp = Str::toCamelCase($property['prop']);

            $result .= PHP_EOL
                . "\tpublic function get{$camelProp}(): {$property['type']}" . PHP_EOL
                . "\t{" . PHP_EOL
                . "\t\treturn \$this->{$property['prop']};" . PHP_EOL
                . "\t}" . PHP_EOL;

            $result .= PHP_EOL
                . "\tpublic function set{$camelProp}({$property['type']} \${$property['prop']}): void" . PHP_EOL
                . "\t{" . PHP_EOL
                . "\t\t\$this->{$property['prop']} = \${$property['prop']};" . PHP_EOL
                . "\t}" . PHP_EOL;
        }

        return $result;
    }
}