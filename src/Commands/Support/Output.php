<?php

namespace Azulphp\Commands\Support;

/**
 * Manage console outputs.
 */
class Output
{
    const string RESET = '0m';
    const string BLACK = '30m';
    const string RED = '31m';
    const string GREEN = '32m';
    const string YELLOW = '33m';
    const string BLUE = '34m';
    const string MAGENTA = '35m';
    const string CYAN = '36m';
    const string WHITE = '37m';

    /**
     * Print new line.
     *
     * @param  string  $output
     * @param  string  $color
     * @return void
     */
    public function line(string $output, string $color = self::RESET): void
    {
        echo "\033[$color$output\033[0m\n";
    }

    /**
     * Print success line.
     *
     * @param  string  $output
     * @return void
     */
    public function success(string $output): void
    {
        $this->line("[Success!]:: $output", self::GREEN);
    }

    /**
     * Print error line.
     *
     * @param  string  $output
     * @return void
     */
    public function error(string $output): void
    {
        $this->line("[Error!]:: $output", self::RED);
    }

    /**
     * Print warning line.
     *
     * @param  string  $output
     * @return void
     */
    public function warning(string $output): void
    {
        $this->line("[Waring!]:: $output", self::YELLOW);
    }

    /**
     * Print info line.
     *
     * @param  string  $output
     * @return void
     */
    public function info(string $output): void
    {
        $this->line("[Info!]:: $output", self::BLUE);
    }

    /**
     * Skip line.
     *
     * @return void
     */
    public function skipLine(): void
    {
        echo PHP_EOL;
    }
}