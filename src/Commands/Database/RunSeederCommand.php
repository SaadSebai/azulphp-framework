<?php

namespace Azulphp\Commands\Database;

use Azulphp\Commands\ConsoleCommand;
use Exception;

class RunSeederCommand extends ConsoleCommand
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->args[0] = 'Database\\Seeders\\' . $this->args[0];

        $this->run('run', $this->args);
    }
}