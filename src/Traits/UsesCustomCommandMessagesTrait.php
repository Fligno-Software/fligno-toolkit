<?php

namespace Fligno\FlignoToolkit\Traits;

/**
 * Trait UsesCustomCommandMessagesTrait
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
trait UsesCustomCommandMessagesTrait
{
    /**
     * @param string $message
     * @param int|string|null $verbosity
     * @return void
     */
    public function ongoing(string $message, int|string  $verbosity = null): void
    {
        $this->note($message, 'ONGOING', $verbosity);
    }

    /**
     * @param string $message
     * @param int|string|null $verbosity
     * @return void
     */
    public function done(string $message, int|string  $verbosity = null): void
    {
        $this->note($message, 'DONE', $verbosity);
    }

    /**
     * @param string $message
     * @param int|string|null $verbosity
     * @return void
     */
    public function failed(string $message, int|string  $verbosity = null): void
    {
        $this->error('<fg=white;bg=red>[ ERROR ]</> ' . $message, $verbosity);
    }

    public function note(string $message, string $title = 'INFO', int|string  $verbosity = null): void
    {
        $this->info('<fg=white;bg=green>[ ' . $title . ' ]</> ' . $message, $verbosity);
    }
}
