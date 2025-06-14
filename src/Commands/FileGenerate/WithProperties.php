<?php

namespace Azulphp\Commands\FileGenerate;

use Azulphp\Helpers\Str;

trait WithProperties
{
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
     * Create the constructor skeleton that will be printed in the stub.
     *
     * @param  array  $properties
     * @return string
     */
    protected function printConstructor(array $properties): string
    {
        $result = "\tpublic function __construct(";
        $last_key = array_key_last($properties);

        foreach ($properties as $key => $property)
        {
            $result .= "protected {$property['type']} \${$property['prop']}";
            if ($last_key !== $key) $result .= ", ";
        }

        $result .= ")" . PHP_EOL
            . "\t{" . PHP_EOL
            . "\t}". PHP_EOL;

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

    /**
     * Build the sub imports.
     *
     * @param  string  $ignoredNamespace
     * @return string
     */
    protected function imports(string $ignoredNamespace = ''): string
    {
        $imports = '';

        foreach ($this->properties as $key => $property)
        {
            if (str_contains($property['type'], '\\'))
            {
                $this->properties[$key]['type'] = Str::afterLast($property['type'], '\\');

                if ($property['type'] !== "$ignoredNamespace\\{$this->properties[$key]['type']}")
                    $imports .=  PHP_EOL . "use {$property['type']};";
            }
        }

        return $imports !== '' ? $imports . PHP_EOL: $imports;
    }
}