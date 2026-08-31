<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\ExecutionLoop;

use Psy\Exception\InterruptException;
use Psy\Shell;
use Psy\Util\DependencyChecker;

/**
 * A signal handler for interrupting execution with Ctrl-C, used when process forking is disabled.
 */
class SignalHandler extends AbstractListener
{
    private bool $sigintHandlerInstalled = false;
    private bool $restoreStty = false;
    private bool $wasInterrupted = false;
    private ?string $originalStty = null;

    public const PCNTL_FUNCTIONS = [
        'pcntl_signal',
        'pcntl_async_signals',
    ];

    public const POSIX_FUNCTIONS = [
        'posix_isatty',
    ];

    /**
     * Signal handler is supported if pcntl and posix extensions are available.
     */
    public static function isSupported(): bool
    {
        return DependencyChecker::functionsAvailable(self::PCNTL_FUNCTIONS)
            && DependencyChecker::functionsAvailable(self::POSIX_FUNCTIONS);
    }

    /**
     * Save original stty state before the REPL starts.
     */
    public function beforeRun(Shell $shell)
    {
        if (@\posix_isatty(\STDIN)) {
            $this->originalStty = @\shell_exec('stty -g 2>/dev/null');
        }
    }

    /**
     * Install SIGINT handler before executing user code.
     */
    public function onExecute(Shell $shell, string $code)
    {
        $this->wasInterrupted = false;

        // Ensure signal processing is enabled so Ctrl-C can interrupt execution
        if (@\posix_isatty(\STDIN)) {
            @\shell_exec('stty isig 2>/dev/null');
            $this->restoreStty = true;
        }

        \pcntl_async_signals(true);

        // Install SIGINT handler that throws exception during execution
        $interrupted = &$this->wasInterrupted;
        $this->sigintHandlerInstalled = \pcntl_signal(\SIGINT, function () use (&$interrupted) {
            $interrupted = true;
            throw new InterruptException('Ctrl+C');
        });

        return null;
    }

    /**
     * Called at the end of each loop.
     *
     * Restores terminal state and clears stdin if execution was interrupted.
     */
    public function afterLoop(Shell $shell)
    {
        // Restore default SIGINT handler after execution
        if ($this->sigintHandlerInstalled) {
            \pcntl_signal(\SIGINT, \SIG_DFL);
            $this->sigintHandlerInstalled = false;
        }

        // Restore terminal to raw mode after execution
        // This prevents Ctrl-C at the prompt from generating SIGINT
        if ($this->restoreStty) {
            @\shell_exec('stty -isig 2>/dev/null');
            $this->restoreStty = false;
        }

        // Clear any pending input from the interrupted stdin stream
        // The SIGINT may have left the stream in a bad state
        if ($this->wasInterrupted && \defined('STDIN') && \is_resource(\STDIN)) {
            // Check if the stream is still usable
            $meta = @\stream_get_meta_data(\STDIN);
            if ($meta && !($meta['eof'] ?? false)) {
                // Drain any buffered input, suppressing I/O errors
                @\stream_set_blocking(\STDIN, false);
                while (@\fgetc(\STDIN) !== false) {
                }
                @\stream_set_blocking(\STDIN, true);
            }
            $this->wasInterrupted = false;
        }
    }

    /**
     * Restore original terminal state when the REPL exits.
     */
    public function afterRun(Shell $shell, int $exitCode = 0)
    {
        if ($this->originalStty !== null) {
            @\shell_exec('stty '.\escapeshellarg(\trim($this->originalStty)).' 2>/dev/null');
        }
    }
}
