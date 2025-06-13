<?php

namespace Azulphp\Commands\Database;

use Azulphp\Commands\ConsoleCommand;
use Azulphp\Core\App;
use Azulphp\Database\Database;
use Exception;

class MigrationCommand extends ConsoleCommand
{
    private Database $db;

    /**
     * @throws Exception
     */
    public function __construct(protected array $args = [], protected array $flags = [])
    {
        parent::__construct($args, $flags);

        $this->db = App::resolve(Database::class);
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->migrate();
    }

    /**
     * Create Database tables.
     *
     * @return void
     * @throws Exception
     */
    private function migrate(): void
    {
        foreach ($this->getMigrations() as $file) {
            $migration = require $file;

            if (is_callable($migration)) {
                $migration($this->db);
                $this->output->line('Executed: ' . basename($file));
            }
        }

        $this->output->success('Tables created or already exist.');
        $this->output->skipLine();

        if (in_array('seed', $this->flags))
            $this->run('seed', $this->args);
    }

    /**
     * Get the list of migrations.
     *
     * @return array
     */
    private function getMigrations(): array
    {
        $path = base_path('database/migrations');
        $file = glob($path . '/*.php');

        if ($file)
            sort($file);

        return $file;
    }
}