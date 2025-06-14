<?php

namespace Azulphp\Core;

use Dotenv\Dotenv;

class Env
{
    /**
     * Set the env.
     *
     * @param  string  $path
     * @return void
     */
    public static function set(string $path): void
    {
        $dotenv = Dotenv::createImmutable($path);
        $dotenv->load();
    }

    /**
     * Get env variable.
     *
     * @param  string  $key
     * @return false|array|string
     */
    public static function get(string $key): false|array|string
    {
        return $_ENV[$key] ?? false;
    }
}