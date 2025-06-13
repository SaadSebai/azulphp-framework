<?php

namespace Azulphp\Core\ServiceProviders;

use Azulphp\Database\Database;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Database::class, function () {
            $config = require base_path('config/database.php');

            return new Database($config);
        });
    }

    public function boot(): void
    {
        //
    }
}