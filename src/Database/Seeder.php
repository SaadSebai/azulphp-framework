<?php

namespace Azulphp\Database;

use Azulphp\App;
use Azulphp\Commands\CommandManager;
use Azulphp\Commands\Runnable;
use Azulphp\Commands\Support\Output;
use Exception;

/**
 * Manage and execute seeders.
 */
abstract class Seeder implements Runnable
{
    protected Output $output;

    public function __construct()
    {
        $this->output = new Output();
    }

    /**
     * Run nested Seeders.
     *
     * @throws Exception
     */
    protected function call(array $classes): void
    {
        foreach ($classes as $class)
        {
            App::resolve(CommandManager::class)->handle(['run', $class]);
        }
    }

    abstract public function handle(): void;
}