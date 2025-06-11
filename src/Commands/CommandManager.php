<?php

namespace Azulphp\Commands;

use Azulphp\Commands\Support\Output;

class CommandManager
{
    protected Output $output;

    public function __construct(protected array $commands)
    {
        $this->output = new Output();
    }

    /**
     * Handle command lines.
     *
     * @param  array|null  $args
     * @return void
     */
    public function handle(?array $args = null): void
    {
        $args ??= $this->getDefaultArgs();

        $commandName = $args[0];

        if (isset($commandName) && isset($this->commands[$commandName]))
        {
            [$inputs, $flags] = $this->getInputsAndCommands($args);

            (new $this->commands[$commandName]($inputs, $flags))->handle();
        }
        else
        {
            $this->output->error('This command does not exist.');
        }
    }

    /**
     * Get the console command's args.
     *
     * @return array
     */
    public function getDefaultArgs(): array
    {
        return array_slice($_SERVER['argv'], 1);
    }

    /**
     * Get List of inputs and flags.
     *
     * @param  $args
     * @return array
     */
    protected function getInputsAndCommands($args): array
    {
        $args = array_slice($args, 1);

        $inputs = $flags = [];

        foreach ($args as $input) {
            if (str_starts_with($input, '--'))
                $flags[] = substr($input, 2);
            else
                $inputs[] = $input;
        }

        return [$inputs, $flags];
    }
}