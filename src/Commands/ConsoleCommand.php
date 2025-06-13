<?php

namespace Azulphp\Commands;

use AllowDynamicProperties;
use Azulphp\Commands\Support\Input;
use Azulphp\Commands\Support\Output;
use Azulphp\Core\App;
use Exception;

/**
 * Manage console commands.
 */
#[AllowDynamicProperties]
abstract class ConsoleCommand
{
    protected Output $output;
    protected Input $input;

    public function __construct(protected array $args = [], protected array $flags = [])
    {
        $this->setArgs($this->args);
        $this->output = new Output();
        $this->input = new Input();
    }

    /**
     * Create properties based on the giving args.
     *
     * @param  array  $args
     * @return void
     */
    protected function setArgs(array $args): void
    {
        foreach ($args as $key => $value) {
            if (str_contains($value, '='))
            {
                [$varName, $varValue] = explode('=', $value, 2);
                $this->{$varName} = $varValue;
            }
            else
            {
                $this->{'arg_' . $key} = $value;
            }
        }
    }

    /**
     * Run another command.
     *
     * @throws Exception
     */
    protected function run(string $command, array $args): void
    {
        App::resolve(CommandManager::class)->handle([$command, ...$args]);
    }

    abstract public function handle(): void;
}