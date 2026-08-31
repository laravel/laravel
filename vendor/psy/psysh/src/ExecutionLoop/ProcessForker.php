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

use Psy\Context;
use Psy\Exception\BreakException;
use Psy\Exception\InterruptException;
use Psy\Shell;
use Psy\Util\DependencyChecker;

/**
 * An execution loop listener that forks the process before executing code.
 *
 * This is awesome, as the session won't die prematurely if user input includes
 * a fatal error, such as redeclaring a class or function.
 */
class ProcessForker extends AbstractListener
{
    private ?int $savegame = null;
    /** @var resource */
    private $up;
    private bool $sigintHandlerInstalled = false;
    private bool $restoreStty = false;
    private ?string $originalStty = null;

    public const PCNTL_FUNCTIONS = [
        'pcntl_fork',
        'pcntl_signal_dispatch',
        'pcntl_signal',
        'pcntl_waitpid',
        'pcntl_wexitstatus',
    ];

    public const POSIX_FUNCTIONS = [
        'posix_getpid',
        'posix_kill',
    ];

    /**
     * Process forker is supported if pcntl and posix extensions are available.
     */
    public static function isSupported(): bool
    {
        return DependencyChecker::functionsAvailable(self::PCNTL_FUNCTIONS)
            && DependencyChecker::functionsAvailable(self::POSIX_FUNCTIONS);
    }

    /**
     * Verify that all required pcntl functions are, in fact, available.
     *
     * @deprecated
     */
    public static function isPcntlSupported(): bool
    {
        return DependencyChecker::functionsAvailable(self::PCNTL_FUNCTIONS);
    }

    /**
     * Check whether required pcntl functions are disabled.
     *
     * @deprecated
     */
    public static function disabledPcntlFunctions()
    {
        return DependencyChecker::functionsDisabled(self::PCNTL_FUNCTIONS);
    }

    /**
     * Verify that all required posix functions are, in fact, available.
     *
     * @deprecated
     */
    public static function isPosixSupported(): bool
    {
        return DependencyChecker::functionsAvailable(self::POSIX_FUNCTIONS);
    }

    /**
     * Check whether required posix functions are disabled.
     *
     * @deprecated
     */
    public static function disabledPosixFunctions()
    {
        return DependencyChecker::functionsDisabled(self::POSIX_FUNCTIONS);
    }

    /**
     * Forks into a main and a loop process.
     *
     * The loop process will handle the evaluation of all instructions, then
     * return its state via a socket upon completion.
     *
     * @param Shell $shell
     */
    public function beforeRun(Shell $shell)
    {
        // Temporarily disable socket timeout for IPC sockets, to avoid losing our child process
        // communication after 60 seconds.
        $originalTimeout = @\ini_set('default_socket_timeout', '-1');

        list($up, $down) = \stream_socket_pair(\STREAM_PF_UNIX, \STREAM_SOCK_STREAM, \STREAM_IPPROTO_IP);

        if ($originalTimeout !== false) {
            @\ini_set('default_socket_timeout', $originalTimeout);
        }

        if (!$up) {
            throw new \RuntimeException('Unable to create socket pair');
        }

        $pid = \pcntl_fork();
        if ($pid < 0) {
            throw new \RuntimeException('Unable to start execution loop');
        } elseif ($pid > 0) {
            // This is the main (parent) process. Install SIGINT handler and wait for child.

            // We won't be needing this one.
            \fclose($up);

            // Install SIGINT handler in parent to interrupt child
            \pcntl_async_signals(true);
            $interrupted = false;
            $sigintHandlerInstalled = \pcntl_signal(\SIGINT, function () use (&$interrupted, $pid) {
                $interrupted = true;
                // Send SIGINT to child so it can handle interruption gracefully
                \posix_kill($pid, \SIGINT);
            });

            // Wait for a return value from the loop process.
            $read = [$down];
            $write = null;
            $except = null;

            do {
                if ($interrupted) {
                    // Wait for child to exit (it should handle SIGINT gracefully)
                    \pcntl_waitpid($pid, $status);

                    // Try to read any final output from child before it exited
                    $content = @\stream_get_contents($down);
                    \fclose($down);

                    if ($sigintHandlerInstalled) {
                        \pcntl_signal(\SIGINT, \SIG_DFL);
                    }

                    $this->clearStdinBuffer();

                    // Restore scope variables and exit code if child sent any
                    // If child didn't send data, use the actual process exit status
                    $exitCode = \pcntl_wexitstatus($status);
                    if ($content) {
                        $data = @\unserialize($content);
                        if (\is_array($data) && isset($data['exitCode'], $data['scopeVars'])) {
                            $exitCode = $data['exitCode'];
                            $shell->setScopeVariables($data['scopeVars']);
                        }
                    }

                    throw new BreakException('Exiting main thread', $exitCode);
                }

                $n = @\stream_select($read, $write, $except, null);

                if ($n === 0) {
                    throw new \RuntimeException('Process timed out waiting for execution loop');
                }

                if ($n === false) {
                    $err = \error_get_last();
                    $errMessage = \is_array($err) ? ($err['message'] ?? null) : null;

                    // If there's no error message, or it's an interrupted system call, just retry
                    if ($errMessage === null || \stripos($errMessage, 'interrupted system call') !== false) {
                        continue;
                    }

                    throw new \RuntimeException(\sprintf('Error waiting for execution loop: %s', $errMessage));
                }
            } while ($n < 1);

            $content = \stream_get_contents($down);
            \fclose($down);

            // Wait for child to exit and get its exit status
            \pcntl_waitpid($pid, $status);

            // Restore default SIGINT handler
            if ($sigintHandlerInstalled) {
                \pcntl_signal(\SIGINT, \SIG_DFL);
            }

            // If child didn't send data, use the actual process exit status
            $exitCode = \pcntl_wexitstatus($status);
            if ($content) {
                $data = @\unserialize($content);
                if (\is_array($data) && isset($data['exitCode'], $data['scopeVars'])) {
                    $exitCode = $data['exitCode'];
                    $shell->setScopeVariables($data['scopeVars']);
                }
            }

            throw new BreakException('Exiting main thread', $exitCode);
        }

        // This is the child process. It's going to do all the work.
        if (!@\cli_set_process_title('psysh (loop)')) {
            // Fall back to `setproctitle` if that wasn't succesful.
            if (\function_exists('setproctitle')) {
                @\setproctitle('psysh (loop)');
            }
        }

        // We won't be needing this one.
        \fclose($down);

        // Save this; we'll need to close it in `afterRun`
        $this->up = $up;

        // Save original stty state so we can restore on exit
        if (@\posix_isatty(\STDIN)) {
            $this->originalStty = @\shell_exec('stty -g 2>/dev/null');
        }
    }

    /**
     * Install SIGINT handler before executing user code.
     */
    public function onExecute(Shell $shell, string $code)
    {
        // Only handle SIGINT in the child process
        if (isset($this->up)) {
            // Ensure signal processing is enabled so Ctrl-C can interrupt execution
            if (@\posix_isatty(\STDIN)) {
                @\shell_exec('stty isig 2>/dev/null');
                $this->restoreStty = true;
            }

            \pcntl_async_signals(true);

            // Install SIGINT handler that throws exception during execution
            \pcntl_signal(\SIGINT, function () {
                throw new InterruptException('Ctrl+C');
            });
        }

        return null;
    }

    /**
     * Create a savegame at the start of each loop iteration.
     *
     * @param Shell $shell
     */
    public function beforeLoop(Shell $shell)
    {
        $this->createSavegame();
    }

    /**
     * Clean up old savegames at the end of each loop iteration.
     *
     * Restores terminal state and clears stdin if execution was interrupted.
     */
    public function afterLoop(Shell $shell)
    {
        // Only handle cleanup in child process
        if (isset($this->up)) {
            // Restore default SIGINT handler after execution
            if (!$this->sigintHandlerInstalled) {
                \pcntl_signal(\SIGINT, \SIG_DFL);
            }

            // Restore terminal to raw mode after execution
            // This prevents Ctrl-C at the prompt from generating SIGINT
            if ($this->restoreStty) {
                @\shell_exec('stty -isig 2>/dev/null');
                $this->restoreStty = false;
            }
        }

        // if there's an old savegame hanging around, let's kill it.
        if (isset($this->savegame)) {
            \posix_kill($this->savegame, \SIGKILL);
            \pcntl_signal_dispatch();
        }
    }

    /**
     * After the REPL session ends, send the scope variables back up to the main
     * thread (if this is a child thread).
     *
     * {@inheritdoc}
     */
    public function afterRun(Shell $shell, int $exitCode = 0)
    {
        // We're a child thread. Send the scope variables and exit code back up to the main thread.
        if (isset($this->up)) {
            $data = $this->serializeReturn($exitCode, $shell->getScopeVariables(false));

            // Suppress errors in case the pipe is broken (e.g., if parent was interrupted)
            @\fwrite($this->up, $data);
            @\fclose($this->up);

            // Restore original terminal state before exiting.
            //
            // We set `stty isig` during execution, so Ctrl-C can interrupt, and
            // `stty -isig` after, so readline can handle it at the prompt.
            // Let's put things back the way we found them.
            if ($this->originalStty !== null) {
                @\shell_exec('stty '.\escapeshellarg(\trim($this->originalStty)).' 2>/dev/null');
            }

            \posix_kill(\posix_getpid(), \SIGKILL);
        }
    }

    /**
     * Create a savegame fork.
     *
     * The savegame contains the current execution state, and can be resumed in
     * the event that the worker dies unexpectedly (for example, by encountering
     * a PHP fatal error).
     */
    private function createSavegame()
    {
        // the current process will become the savegame
        $this->savegame = \posix_getpid();

        $pid = \pcntl_fork();
        if ($pid < 0) {
            throw new \RuntimeException('Unable to create savegame fork');
        } elseif ($pid > 0) {
            // we're the savegame now... let's wait and see what happens
            \pcntl_waitpid($pid, $status);

            // worker exited cleanly, let's bail
            if (!\pcntl_wexitstatus($status)) {
                \posix_kill(\posix_getpid(), \SIGKILL);
            }

            // worker didn't exit cleanly, we'll need to have another go
            // @phan-suppress-next-line PhanPossiblyInfiniteRecursionSameParams - recursion exits via posix_kill above
            $this->createSavegame();
        }
    }

    /**
     * Clear stdin buffer after interruption, in case SIGINT left the stream in a bad state.
     */
    private function clearStdinBuffer(): void
    {
        if (!\defined('STDIN') || !\is_resource(\STDIN)) {
            return;
        }

        // Check if the stream is still usable
        $meta = @\stream_get_meta_data(\STDIN);
        if (!$meta || ($meta['eof'] ?? false)) {
            return;
        }

        // Drain any buffered input, suppressing I/O errors
        @\stream_set_blocking(\STDIN, false);
        while (@\fgetc(\STDIN) !== false) {
        }
        @\stream_set_blocking(\STDIN, true);
    }

    /**
     * Serialize exit code and scope variables for transmission to parent process.
     *
     * A naïve serialization will run into issues if there is a Closure or
     * SimpleXMLElement (among other things) in scope when exiting the execution
     * loop. We'll just ignore these unserializable classes, and serialize what
     * we can.
     *
     * @param int   $exitCode  Exit code from the child process
     * @param array $scopeVars Scope variables to serialize
     *
     * @return string Serialized data array containing exitCode and scopeVars
     */
    private function serializeReturn(int $exitCode, array $scopeVars): string
    {
        $serializable = [];

        foreach ($scopeVars as $key => $value) {
            // No need to return magic variables
            if (Context::isSpecialVariableName($key)) {
                continue;
            }

            // Resources and Closures don't error, but they don't serialize well either.
            if (\is_resource($value) || $value instanceof \Closure) {
                continue;
            }

            if (\PHP_VERSION_ID >= 80100 && $value instanceof \UnitEnum) {
                // Enums defined in the REPL session can't be unserialized.
                $ref = new \ReflectionObject($value);
                if (\strpos($ref->getFileName(), ": eval()'d code") !== false) {
                    continue;
                }
            }

            try {
                @\serialize($value);
                $serializable[$key] = $value;
            } catch (\Throwable $e) {
                // we'll just ignore this one...
            }
        }

        return @\serialize([
            'exitCode'  => $exitCode,
            'scopeVars' => $serializable,
        ]);
    }
}
