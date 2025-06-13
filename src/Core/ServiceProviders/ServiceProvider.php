<?php

namespace Azulphp\Core\ServiceProviders;

use Azulphp\Core\Container;

abstract class ServiceProvider
{
    public function __construct(protected Container $app)
    {
        //
    }

    abstract public function register(): void;
    abstract public function boot(): void;
}