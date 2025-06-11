<?php

namespace Azulphp\Commands\Support;

/**
 * Manage console inputs.
 */
class Input
{
    /**
     * Read console input.
     *
     * @return false|string
     */
    public function line(): false|string
    {
        echo '> ';
        return readline();
    }

    /**
     * Accept a required console input.
     *
     * @param  callable|null  $before
     * @param  callable|null  $validation
     * @return false|string
     */
    public function requireLine(callable $before = null, callable $validation = null): false|string
    {
        $entered = false;
        $line = '';

        while (!$entered)
        {
            $before();
            $line = $this->line();

            if ($validation) $validation($line, $entered);
            elseif ($line !== '') $entered = true;
        }

        return $line;
    }
}