<?php

namespace Azulphp\Commands;

class ServeCommand extends ConsoleCommand
{
    public function handle(): void
    {
        passthru('php -S localhost:8000 -t public');
    }
}