<?php

namespace Azulphp\Core\ServiceProviders;

use Azulphp\Commands\CommandManager;
use Azulphp\Database\Database;

class CommandServiceManager extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CommandManager::class, function () {
            $config = require base_path('config/commands.php');

            return new CommandManager($config);
        });
    }

    public function boot(): void
    {
        //
    }
}