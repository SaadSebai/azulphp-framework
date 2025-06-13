<?php

namespace Azulphp\Core\ServiceProviders;

use Azulphp\Commands\CommandManager;
use Azulphp\Core\Config;
use Azulphp\Core\Container;

class ConfigServiceProvider extends ServiceProvider
{
    public function __construct(Container $app)
    {
        parent::__construct($app);
    }

    public function register(): void
    {
        $this->app->bind(Config::class, function () {
            $config = require base_path('config/app.php');

            return new Config($config);
        });
    }

    public function boot(): void
    {
        //
    }
}