<?php

use Azulphp\Core\Env;

function env(string $key): false|array|string
{
    return Env::get($key);
}

function base_path(string $path): string
{
    return BASE_PATH . $path;
}