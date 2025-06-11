<?php

namespace Azulphp\Exceptions;

use Azulphp\Commands\Support\Output;
use Exception;

class ConsoleException extends Exception
{
    /**
     * @throws ConsoleException
     */
    public static function throw(string $message)
    {
        $output = new Output();

        $output->error($message);

        throw new static();
    }
}