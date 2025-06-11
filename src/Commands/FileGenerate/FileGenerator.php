<?php

namespace Azulphp\Commands\FileGenerate;

use Azulphp\Commands\Database\ConsoleCommand;
use Azulphp\Helpers\Str;

/**
 * Build File Generator command.
 */
abstract class FileGenerator extends ConsoleCommand
{
    /**
     * Generate Class form stub.
     *
     * @param  string  $stubPath
     * @param  string  $targetPath
     * @param  array  $replacements
     * @return void
     */
    protected function generate(string $stubPath, string $targetPath, array $replacements): void
    {
        $stub = file_get_contents($stubPath);

        foreach ($replacements as $key => $value) {
            $stub = str_replace('{{ ' . $key . ' }}', $value, $stub);
        }

        if (!file_exists("$targetPath.php"))
        {
            @mkdir(dirname($targetPath), recursive: true);
            file_put_contents("$targetPath.php", $stub);

            $this->output->success("$targetPath has been created.");
        }
        else
            $this->output->warning("$targetPath already exists!!");
    }

    /**
     * Get the stub path.
     *
     * @param  string  $stubPath
     * @return string
     */
    protected function stubPath(string $stubPath): string
    {
        return base_path("app/Core/Commands/FileGenerate/stubs/$stubPath.stub");
    }

    /**
     * Get the giving class name.
     *
     * @return string
     */
    protected function getClass(): string
    {
        return Str::afterLast($this->args[0]);
    }

    /**
     * Build the namespace.
     *
     * @param  string  $targetPath
     * @return string
     */
    protected function getNamespace(string $targetPath): string
    {
        if (($dirname = dirname($targetPath)) !== '.')
            return '\\' . Str::pathToNameSpace($dirname);
        else return '';
    }
}