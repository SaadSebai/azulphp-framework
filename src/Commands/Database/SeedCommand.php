<?php

namespace Azulphp\Commands\Database;

use Azulphp\Commands\ConsoleCommand;
use Exception;

class SeedCommand extends ConsoleCommand
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->run('run', ['Database\\Seeders\\DatabaseSeeder']);

        $this->output->success('Records created');
        $this->output->skipLine();
    }
}