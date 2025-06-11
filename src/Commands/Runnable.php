<?php

namespace Azulphp\Commands;

interface Runnable
{
    /**
     * Class can be called in the console with the 'run' command.
     *
     * @return void
     */
    public function handle(): void;
}