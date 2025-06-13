<?php

namespace Azulphp\Core;

class Config
{
    public function __construct(protected array $configs)
    {
        //
    }

    /**
     * Retrieve the config.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getConfig(string $key): mixed
    {
        return $this->configs[$key] ?? null;
    }
}