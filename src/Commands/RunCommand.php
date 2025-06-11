<?php

namespace Azulphp\Commands;

use Azulphp\Commands\Database\ConsoleCommand;
use Azulphp\Exceptions\ConsoleException;
use Exception;

class RunCommand extends ConsoleCommand
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $properties = array_slice($this->args, 1);

        if (($runnable = new $this->args[0](...$properties)) instanceof Runnable)
        {
            $runnable->handle();
        }
        else
        {
            ConsoleException::throw("Class {$this->args[0]} most be an instance of " . Runnable::class . ".");
        }
    }
}