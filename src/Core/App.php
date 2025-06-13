<?php

namespace Azulphp\Core;

use Exception;

/**
 * Application container.
 */
class App
{
    protected static Container $container;

    public static function setContainer($container): void
    {
        static::$container = $container;
    }

    public static function container(): Container
    {
        return static::$container;
    }

    public static function bind($key, $resolver): void
    {
        static::container()->bind($key, $resolver);
    }

    /**
     * Retrieve from container
     *
     * @throws Exception
     */
    public static function resolve($key)
    {
        return static::container()->resolve($key);
    }
}
