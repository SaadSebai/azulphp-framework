<?php

namespace Azulphp\Commands\FileGenerate;

use Azulphp\Commands\ConsoleCommand;
use Azulphp\Helpers\Str;

/**
 * Build File Generator command.
 */
abstract class FileGenerator extends ConsoleCommand
{
    protected string $fileName;

    public function __construct(array $args = [], array $flags = [])
    {
        parent::__construct($args, $flags);
        $this->fileName = $this->getInputFileName();
    }

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
        return __DIR__ . "/stubs/$stubPath.stub";
    }

    /**
     * Get the giving file name without including the path if it was entered.
     *
     * @return string
     */
    protected function getFileNameWithoutPath(): string
    {
        return Str::afterLast($this->fileName);
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

    /**
     * Ask the user to enter file name if he didn't provide it in the command.
     *
     * @return string
     */
    protected function getInputFileName(): string
    {
        if (!isset($this->args[0]))
        {
            return $this->input->requireLine(
                before: fn () => $this->output->secondary("Please prove your class name: (*)")
            );
        }

        return $this->args[0];
    }
}