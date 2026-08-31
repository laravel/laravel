<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Helper;

/**
 * TerminalInputHelper stops Ctrl-C and similar signals from leaving the terminal in
 * an unusable state if its settings have been modified when reading user input.
 * This can be an issue on non-Windows platforms.
 *
 * Usage:
 *
 *     $inputHelper = new TerminalInputHelper($inputStream);
 *
 *     ...change terminal settings
 *
 *     // Wait for input before all input reads
 *     $inputHelper->waitForInput();
 *
 *     ...read input
 *
 *     // Call finish to restore terminal settings and signal handlers
 *     $inputHelper->finish()
 *
 * @internal
 */
final class TerminalInputHelper
{
    /** @var resource */
    private $inputStream;
    private bool $isStdin;
    private string $initialState = '';
    private int $signalToKill = 0;
    private array $signalHandlers = [];
    private array $targetSignals = [];
    private bool $withStty;

    /**
     * @param resource $inputStream
     *
     * @throws \RuntimeException If unable to read terminal settings
     */
    public function __construct($inputStream, bool $withStty = true)
    {
        $this->inputStream = $inputStream;
        $this->isStdin = 'php://stdin' === stream_get_meta_data($inputStream)['uri'];
        $this->withStty = $withStty;

        if ($withStty) {
            if (!\is_string($state = shell_exec('stty -g'))) {
                throw new \RuntimeException('Unable to read the terminal settings.');
            }

            $this->initialState = $state;

            $this->createSignalHandlers();
        }
    }

    /**
     * Waits for input.
     */
    public function waitForInput(): void
    {
        if ($this->isStdin) {
            $r = [$this->inputStream];
            $w = [];

            // Allow signal handlers to run
            while (0 === @stream_select($r, $w, $w, 0, 100)) {
                $r = [$this->inputStream];
            }
        }

        if ($this->withStty) {
            $this->checkForKillSignal();
        }
    }

    /**
     * Restores terminal state and signal handlers.
     */
    public function finish(): void
    {
        if (!$this->withStty) {
            return;
        }

        // Safeguard in case an unhandled kill signal exists
        $this->checkForKillSignal();
        shell_exec('stty '.$this->initialState);
        $this->signalToKill = 0;

        foreach ($this->signalHandlers as $signal => $originalHandler) {
            pcntl_signal($signal, $originalHandler);
        }
        $this->signalHandlers = [];
        $this->targetSignals = [];
    }

    private function createSignalHandlers(): void
    {
        if (!\function_exists('pcntl_async_signals') || !\function_exists('pcntl_signal')) {
            return;
        }

        pcntl_async_signals(true);
        $this->targetSignals = [\SIGINT, \SIGQUIT, \SIGTERM];

        foreach ($this->targetSignals as $signal) {
            $this->signalHandlers[$signal] = pcntl_signal_get_handler($signal);

            pcntl_signal($signal, function ($signal) {
                // Save current state, then restore to initial state
                $currentState = shell_exec('stty -g');
                shell_exec('stty '.$this->initialState);
                $originalHandler = $this->signalHandlers[$signal];

                if (\is_callable($originalHandler)) {
                    $originalHandler($signal);
                    // Handler did not exit, so restore to current state
                    shell_exec('stty '.$currentState);

                    return;
                }

                // Not a callable, so SIG_DFL or SIG_IGN
                if (\SIG_DFL === $originalHandler) {
                    $this->signalToKill = $signal;
                }
            });
        }
    }

    private function checkForKillSignal(): void
    {
        if (\in_array($this->signalToKill, $this->targetSignals, true)) {
            // Try posix_kill
            if (\function_exists('posix_kill')) {
                pcntl_signal($this->signalToKill, \SIG_DFL);
                posix_kill(getmypid(), $this->signalToKill);
            }

            // Best attempt fallback
            exit(128 + $this->signalToKill);
        }
    }
}
