<?php

namespace Azulphp\Commands\Database;

use Database\Seeders\DatabaseSeeder;
use Exception;

class SeedCommand extends ConsoleCommand
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->run('run', [DatabaseSeeder::class]);

        $this->output->success('Records created');
        $this->output->skipLine();
    }
}