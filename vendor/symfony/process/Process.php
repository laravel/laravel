<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Process;

use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Pipes\UnixPipes;
use Symfony\Component\Process\Pipes\WindowsPipes;

/**
 * Process is a thin wrapper around proc_* functions to easily
 * start independent PHP processes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Romain Neutron <imprec@gmail.com>
 *
 * @implements \IteratorAggregate<string, string>
 */
class Process implements \IteratorAggregate
{
    public const ERR = 'err';
    public const OUT = 'out';

    public const STATUS_READY = 'ready';
    public const STATUS_STARTED = 'started';
    public const STATUS_TERMINATED = 'terminated';

    public const STDIN = 0;
    public const STDOUT = 1;
    public const STDERR = 2;

    // Timeout Precision in seconds.
    public const TIMEOUT_PRECISION = 0.2;

    public const ITER_NON_BLOCKING = 1; // By default, iterating over outputs is a blocking call, use this flag to make it non-blocking
    public const ITER_KEEP_OUTPUT = 2;  // By default, outputs are cleared while iterating, use this flag to keep them in memory
    public const ITER_SKIP_OUT = 4;     // Use this flag to skip STDOUT while iterating
    public const ITER_SKIP_ERR = 8;     // Use this flag to skip STDERR while iterating

    /**
     * Maximum number of UTF-16 code units allowed in the Windows environment block.
     *
     * The Win32 CreateProcess API encodes env vars as KEY=VALUE\0 in UTF-16LE,
     * terminated by an extra \0. Exceeding this limit causes proc_open() to hang
     * silently rather than returning false.
     *
     * @see https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessa
     */
    private const WINDOWS_ENV_BLOCK_MAX_LENGTH = 32767;

    /**
     * @var \Closure('out'|'err', string):bool|null
     */
    private ?\Closure $callback = null;
    private array|string $commandline;
    private ?string $cwd;
    private array $env = [];
    /** @var resource|string|\Iterator|null */
    private $input;
    private ?float $starttime = null;
    private ?float $lastOutputTime = null;
    private ?float $timeout = null;
    private ?float $idleTimeout = null;
    private ?int $exitcode = null;
    private array $fallbackStatus = [];
    private array $processInformation;
    private bool $outputDisabled = false;
    /** @var resource */
    private $stdout;
    /** @var resource */
    private $stderr;
    /** @var resource|null */
    private $process;
    private string $status = self::STATUS_READY;
    private int $incrementalOutputOffset = 0;
    private int $incrementalErrorOutputOffset = 0;
    private bool $tty = false;
    private bool $pty;
    private array $options = ['suppress_errors' => true, 'bypass_shell' => true];
    private array $ignoredSignals = [];

    private WindowsPipes|UnixPipes $processPipes;

    private ?int $latestSignal = null;

    private static ?bool $sigchild = null;
    private static array $executables = [];

    /**
     * Exit codes translation table.
     *
     * User-defined errors must use exit codes in the 64-113 range.
     */
    public static array $exitCodes = [
        0 => 'OK',
        1 => 'General error',
        2 => 'Misuse of shell builtins',

        126 => 'Invoked command cannot execute',
        127 => 'Command not found',
        128 => 'Invalid exit argument',

        // signals
        129 => 'Hangup',
        130 => 'Interrupt',
        131 => 'Quit and dump core',
        132 => 'Illegal instruction',
        133 => 'Trace/breakpoint trap',
        134 => 'Process aborted',
        135 => 'Bus error: "access to undefined portion of memory object"',
        136 => 'Floating point exception: "erroneous arithmetic operation"',
        137 => 'Kill (terminate immediately)',
        138 => 'User-defined 1',
        139 => 'Segmentation violation',
        140 => 'User-defined 2',
        141 => 'Write to pipe with no one reading',
        142 => 'Signal raised by alarm',
        143 => 'Termination (request to terminate)',
        // 144 - not defined
        145 => 'Child process terminated, stopped (or continued*)',
        146 => 'Continue if stopped',
        147 => 'Stop executing temporarily',
        148 => 'Terminal stop signal',
        149 => 'Background process attempting to read from tty ("in")',
        150 => 'Background process attempting to write to tty ("out")',
        151 => 'Urgent data available on socket',
        152 => 'CPU time limit exceeded',
        153 => 'File size limit exceeded',
        154 => 'Signal raised by timer counting virtual time: "virtual timer expired"',
        155 => 'Profiling timer expired',
        // 156 - not defined
        157 => 'Pollable event',
        // 158 - not defined
        159 => 'Bad syscall',
    ];

    /**
     * @param array          $command The command to run and its arguments listed as separate entries
     * @param string|null    $cwd     The working directory or null to use the working dir of the current PHP process
     * @param array|null     $env     The environment variables or null to use the same environment as the current PHP process
     * @param mixed          $input   The input as stream resource, scalar or \Traversable, or null for no input
     * @param int|float|null $timeout The timeout in seconds or null to disable
     *
     * @throws LogicException When proc_open is not installed
     */
    public function __construct(array $command, ?string $cwd = null, ?array $env = null, mixed $input = null, ?float $timeout = 60)
    {
        if (!\function_exists('proc_open')) {
            throw new LogicException('The Process class relies on proc_open, which is not available on your PHP installation.');
        }

        $this->commandline = $command;
        $this->cwd = $cwd;

        // on Windows, if the cwd changed via chdir(), proc_open defaults to the dir where PHP was started
        // on Gnu/Linux, PHP builds with --enable-maintainer-zts are also affected
        // @see : https://bugs.php.net/51800
        // @see : https://bugs.php.net/50524
        if (null === $this->cwd && (\defined('ZEND_THREAD_SAFE') || '\\' === \DIRECTORY_SEPARATOR)) {
            $this->cwd = getcwd();
        }
        if (null !== $env) {
            $this->setEnv($env);
        }

        $this->setInput($input);
        $this->setTimeout($timeout);
        $this->pty = false;
    }

    /**
     * Creates a Process instance as a command-line to be run in a shell wrapper.
     *
     * Command-lines are parsed by the shell of your OS (/bin/sh on Unix-like, cmd.exe on Windows.)
     * This allows using e.g. pipes or conditional execution. In this mode, signals are sent to the
     * shell wrapper and not to your commands.
     *
     * In order to inject dynamic values into command-lines, we strongly recommend using placeholders.
     * This will save escaping values, which is not portable nor secure anyway:
     *
     *   $process = Process::fromShellCommandline('my_command "${:MY_VAR}"');
     *   $process->run(null, ['MY_VAR' => $theValue]);
     *
     * @param string         $command The command line to pass to the shell of the OS
     * @param string|null    $cwd     The working directory or null to use the working dir of the current PHP process
     * @param array|null     $env     The environment variables or null to use the same environment as the current PHP process
     * @param mixed          $input   The input as stream resource, scalar or \Traversable, or null for no input
     * @param int|float|null $timeout The timeout in seconds or null to disable
     *
     * @throws LogicException When proc_open is not installed
     */
    public static function fromShellCommandline(string $command, ?string $cwd = null, ?array $env = null, mixed $input = null, ?float $timeout = 60): static
    {
        $process = new static([], $cwd, $env, $input, $timeout);
        $process->commandline = $command;

        return $process;
    }

    public function __serialize(): array
    {
        throw new \BadMethodCallException('Cannot serialize '.__CLASS__);
    }

    public function __unserialize(array $data): void
    {
        throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
    }

    public function __destruct()
    {
        if ($this->options['create_new_console'] ?? false) {
            $this->processPipes->close();
        } else {
            $this->stop(0);
        }
    }

    public function __clone()
    {
        $this->resetProcessData();
    }

    /**
     * Runs the process.
     *
     * The callback receives the type of output (out or err) and
     * some bytes from the output in real-time. It allows to have feedback
     * from the independent process during execution.
     *
     * The STDOUT and STDERR are also available after the process is finished
     * via the getOutput() and getErrorOutput() methods.
     *
     * @param (callable('out'|'err', string):void)|null $callback A PHP callback to run whenever there is some
     *                                                            output available on STDOUT or STDERR
     *
     * @return int The exit status code
     *
     * @throws ProcessStartFailedException When process can't be launched
     * @throws RuntimeException            When process is already running
     * @throws ProcessTimedOutException    When process timed out
     * @throws ProcessSignaledException    When process stopped after receiving signal
     * @throws LogicException              In case a callback is provided and output has been disabled
     *
     * @final
     */
    public function run(?callable $callback = null, array $env = []): int
    {
        $this->start($callback, $env);

        return $this->wait();
    }

    /**
     * Runs the process.
     *
     * This is identical to run() except that an exception is thrown if the process
     * exits with a non-zero exit code.
     *
     * @param (callable('out'|'err', string):void)|null $callback A PHP callback to run whenever there is some
     *                                                            output available on STDOUT or STDERR
     *
     * @return $this
     *
     * @throws ProcessFailedException   When process didn't terminate successfully
     * @throws RuntimeException         When process can't be launched
     * @throws RuntimeException         When process is already running
     * @throws ProcessTimedOutException When process timed out
     * @throws ProcessSignaledException When process stopped after receiving signal
     * @throws LogicException           In case a callback is provided and output has been disabled
     *
     * @final
     */
    public function mustRun(?callable $callback = null, array $env = []): static
    {
        if (0 !== $this->run($callback, $env)) {
            throw new ProcessFailedException($this);
        }

        return $this;
    }

    /**
     * Starts the process and returns after writing the input to STDIN.
     *
     * This method blocks until all STDIN data is sent to the process then it
     * returns while the process runs in the background.
     *
     * The termination of the process can be awaited with wait().
     *
     * The callback receives the type of output (out or err) and some bytes from
     * the output in real-time while writing the standard input to the process.
     * It allows to have feedback from the independent process during execution.
     *
     * @param (callable('out'|'err', string):void)|null $callback A PHP callback to run whenever there is some
     *                                                            output available on STDOUT or STDERR
     *
     * @throws ProcessStartFailedException When process can't be launched
     * @throws RuntimeException            When process is already running
     * @throws LogicException              In case a callback is provided and output has been disabled
     */
    public function start(?callable $callback = null, array $env = []): void
    {
        if ($this->isRunning()) {
            throw new RuntimeException('Process is already running.');
        }

        $this->resetProcessData();
        $this->starttime = $this->lastOutputTime = microtime(true);
        $this->callback = $this->buildCallback($callback);
        $descriptors = $this->getDescriptors(null !== $callback);

        if ($this->env) {
            $env += '\\' === \DIRECTORY_SEPARATOR ? array_diff_ukey($this->env, $env, 'strcasecmp') : $this->env;
        }

        $env += '\\' === \DIRECTORY_SEPARATOR ? array_diff_ukey($this->getDefaultEnv(), $env, 'strcasecmp') : $this->getDefaultEnv();

        if (\is_array($commandline = $this->commandline)) {
            $commandline = array_values(array_map(strval(...), $commandline));
        } else {
            $commandline = $this->replacePlaceholders($commandline, $env);
        }

        if ('\\' === \DIRECTORY_SEPARATOR) {
            $commandline = $this->prepareWindowsCommandLine($commandline, $env);
        } elseif ($this->isSigchildEnabled()) {
            // last exit code is output on the fourth pipe and caught to work around --enable-sigchild
            $descriptors[3] = ['pipe', 'w'];

            if (\is_array($commandline)) {
                // exec is mandatory to deal with sending a signal to the process
                $commandline = 'exec '.$this->buildShellCommandline($commandline);
            }

            // See https://unix.stackexchange.com/questions/71205/background-process-pipe-input
            $commandline = '{ ('.$commandline.') <&3 3<&- 3>/dev/null & } 3<&0;';
            $commandline .= 'pid=$!; echo $pid >&3; wait $pid 2>/dev/null; code=$?; echo $code >&3; exit $code';
        }

        $envPairs = [];
        foreach ($env as $k => $v) {
            if (!\is_scalar($v ?? '') && !$v instanceof \Stringable) {
                continue;
            }

            if (false !== $v && !\in_array($k = (string) $k, ['', 'argc', 'argv', 'ARGC', 'ARGV'], true) && !str_contains($k, '=') && !str_contains($k, "\0")) {
                $envPairs[] = $k.'='.$v;
            }
        }

        if ('\\' === \DIRECTORY_SEPARATOR) {
            $this->validateWindowsEnvBlockSize($envPairs);
        }

        if (!is_dir($this->cwd)) {
            throw new RuntimeException(\sprintf('The provided cwd "%s" does not exist.', $this->cwd));
        }

        $lastError = null;
        set_error_handler(static function ($type, $msg) use (&$lastError) {
            $lastError = $msg;

            return true;
        });

        $oldMask = [];

        if ($this->ignoredSignals && \function_exists('pcntl_sigprocmask')) {
            // we block signals we want to ignore, as proc_open will use fork / posix_spawn which will copy the signal mask this allow to block
            // signals in the child process
            pcntl_sigprocmask(\SIG_BLOCK, $this->ignoredSignals, $oldMask);
        }

        try {
            $process = @proc_open($commandline, $descriptors, $this->processPipes->pipes, $this->cwd, $envPairs, $this->options);

            // Ensure array vs string commands behave the same
            if (!$process && \is_array($commandline)) {
                $process = @proc_open('exec '.$this->buildShellCommandline($commandline), $descriptors, $this->processPipes->pipes, $this->cwd, $envPairs, $this->options);
            }
        } finally {
            if ($this->ignoredSignals && \function_exists('pcntl_sigprocmask')) {
                // we restore the signal mask here to avoid any side effects
                pcntl_sigprocmask(\SIG_SETMASK, $oldMask);
            }

            restore_error_handler();
        }

        if (!$process) {
            throw new ProcessStartFailedException($this, $lastError);
        }
        $this->process = $process;
        $this->status = self::STATUS_STARTED;

        if (isset($descriptors[3])) {
            $this->fallbackStatus['pid'] = (int) fgets($this->processPipes->pipes[3]);
        }

        if ($this->tty) {
            return;
        }

        $this->updateStatus(false);
        $this->checkTimeout();
    }

    /**
     * Restarts the process.
     *
     * Be warned that the process is cloned before being started.
     *
     * @param (callable('out'|'err', string):void)|null $callback A PHP callback to run whenever there is some
     *                                                            output available on STDOUT or STDERR
     *
     * @throws ProcessStartFailedException When process can't be launched
     * @throws RuntimeException            When process is already running
     *
     * @see start()
     *
     * @final
     */
    public function restart(?callable $callback = null, array $env = []): static
    {
        if ($this->isRunning()) {
            throw new RuntimeException('Process is already running.');
        }

        $process = clone $this;
        $process->start($callback, $env);

        return $process;
    }

    /**
     * Waits for the process to terminate.
     *
     * The callback receives the type of output (out or err) and some bytes
     * from the output in real-time while writing the standard input to the process.
     * It allows to have feedback from the independent process during execution.
     *
     * @param (callable('out'|'err', string):void)|null $callback A PHP callback to run whenever there is some
     *                                                            output available on STDOUT or STDERR
     *
     * @return int The exitcode of the process
     *
     * @throws ProcessTimedOutException When process timed out
     * @throws ProcessSignaledException When process stopped after receiving signal
     * @throws LogicException           When process is not yet started
     */
    public function wait(?callable $callback = null): int
    {
        $this->requireProcessIsStarted(__FUNCTION__);

        $this->updateStatus(false);

        if (null !== $callback) {
            if (!$this->processPipes->haveReadSupport()) {
                $this->stop(0);
                throw new LogicException('Pass the callback to the "Process::start" method or call enableOutput to use a callback with "Process::wait".');
            }
            $this->callback = $this->buildCallback($callback);
        }

        do {
            $this->checkTimeout();
            $running = $this->isRunning() && ('\\' === \DIRECTORY_SEPARATOR || $this->processPipes->areOpen());
            $this->readPipes($running, '\\' !== \DIRECTORY_SEPARATOR || !$running);
        } while ($running);

        while ($this->isRunning()) {
            $this->checkTimeout();
            usleep(1000);
        }

        if ($this->processInformation['signaled'] && $this->processInformation['termsig'] !== $this->latestSignal) {
            throw new ProcessSignaledException($this);
        }

        return $this->exitcode;
    }

    /**
     * Waits until the callback returns true.
     *
     * The callback receives the type of output (out or err) and some bytes
     * from the output in real-time while writing the standard input to the process.
     * It allows to have feedback from the independent process during execution.
     *
     * @param (callable('out'|'err', string):bool)|null $callback A PHP callback to run whenever there is some
     *                                                            output available on STDOUT or STDERR
     *
     * @throws RuntimeException         When process timed out
     * @throws LogicException           When process is not yet started
     * @throws ProcessTimedOutException In case the timeout was reached
     */
    public function waitUntil(callable $callback): bool
    {
        $this->requireProcessIsStarted(__FUNCTION__);
        $this->updateStatus(false);

        if (!$this->processPipes->haveReadSupport()) {
            $this->stop(0);
            throw new LogicException('Pass the callback to the "Process::start" method or call enableOutput to use a callback with "Process::waitUntil".');
        }
        $callback = $this->buildCallback($callback);

        $ready = false;
        while (true) {
            $this->checkTimeout();
            $running = '\\' === \DIRECTORY_SEPARATOR ? $this->isRunning() : $this->processPipes->areOpen();
            $output = $this->processPipes->readAndWrite($running, '\\' !== \DIRECTORY_SEPARATOR || !$running);

            foreach ($output as $type => $data) {
                if (3 !== $type) {
                    $ready = $callback(self::STDOUT === $type ? self::OUT : self::ERR, $data) || $ready;
                } elseif (!isset($this->fallbackStatus['signaled'])) {
                    $this->fallbackStatus['exitcode'] = (int) $data;
                }
            }
            if ($ready) {
                return true;
            }
            if (!$running) {
                return false;
            }

            usleep(1000);
        }
    }

    /**
     * Returns the Pid (process identifier), if applicable.
     *
     * @return int|null The process id if running, null otherwise
     */
    public function getPid(): ?int
    {
        return $this->isRunning() ? $this->processInformation['pid'] : null;
    }

    /**
     * Sends a POSIX signal to the process.
     *
     * @param int $signal A valid POSIX signal (see https://php.net/pcntl.constants)
     *
     * @return $this
     *
     * @throws LogicException   In case the process is not running
     * @throws RuntimeException In case --enable-sigchild is activated and the process can't be killed
     * @throws RuntimeException In case of failure
     */
    public function signal(int $signal): static
    {
        $this->doSignal($signal, true);

        return $this;
    }

    /**
     * Disables fetching output and error output from the underlying process.
     *
     * @return $this
     *
     * @throws RuntimeException In case the process is already running
     * @throws LogicException   if an idle timeout is set
     */
    public function disableOutput(): static
    {
        if ($this->isRunning()) {
            throw new RuntimeException('Disabling output while the process is running is not possible.');
        }
        if (null !== $this->idleTimeout) {
            throw new LogicException('Output cannot be disabled while an idle timeout is set.');
        }

        $this->outputDisabled = true;

        return $this;
    }

    /**
     * Enables fetching output and error output from the underlying process.
     *
     * @return $this
     *
     * @throws RuntimeException In case the process is already running
     */
    public function enableOutput(): static
    {
        if ($this->isRunning()) {
            throw new RuntimeException('Enabling output while the process is running is not possible.');
        }

        $this->outputDisabled = false;

        return $this;
    }

    /**
     * Returns true in case the output is disabled, false otherwise.
     */
    public function isOutputDisabled(): bool
    {
        return $this->outputDisabled;
    }

    /**
     * Returns the current output of the process (STDOUT).
     *
     * @throws LogicException in case the output has been disabled
     * @throws LogicException In case the process is not started
     */
    public function getOutput(): string
    {
        $this->readPipesForOutput(__FUNCTION__);

        if (false === $ret = stream_get_contents($this->stdout, -1, 0)) {
            return '';
        }

        return $ret;
    }

    /**
     * Returns the output incrementally.
     *
     * In comparison with the getOutput method which always return the whole
     * output, this one returns the new output since the last call.
     *
     * @throws LogicException in case the output has been disabled
     * @throws LogicException In case the process is not started
     */
    public function getIncrementalOutput(): string
    {
        $this->readPipesForOutput(__FUNCTION__);

        $latest = stream_get_contents($this->stdout, -1, $this->incrementalOutputOffset);
        $this->incrementalOutputOffset = ftell($this->stdout);

        if (false === $latest) {
            return '';
        }

        return $latest;
    }

    /**
     * Returns an iterator to the output of the process, with the output type as keys (Process::OUT/ERR).
     *
     * @param int $flags A bit field of Process::ITER_* flags
     *
     * @return \Generator<string, string>
     *
     * @throws LogicException in case the output has been disabled
     * @throws LogicException In case the process is not started
     */
    public function getIterator(int $flags = 0): \Generator
    {
        $this->readPipesForOutput(__FUNCTION__, false);

        $clearOutput = !(self::ITER_KEEP_OUTPUT & $flags);
        $blocking = !(self::ITER_NON_BLOCKING & $flags);
        $yieldOut = !(self::ITER_SKIP_OUT & $flags);
        $yieldErr = !(self::ITER_SKIP_ERR & $flags);

        while (null !== $this->callback || ($yieldOut && !feof($this->stdout)) || ($yieldErr && !feof($this->stderr))) {
            if ($yieldOut) {
                $out = stream_get_contents($this->stdout, -1, $this->incrementalOutputOffset);

                if (isset($out[0])) {
                    if ($clearOutput) {
                        $this->clearOutput();
                    } else {
                        $this->incrementalOutputOffset = ftell($this->stdout);
                    }

                    yield self::OUT => $out;
                }
            }

            if ($yieldErr) {
                $err = stream_get_contents($this->stderr, -1, $this->incrementalErrorOutputOffset);

                if (isset($err[0])) {
                    if ($clearOutput) {
                        $this->clearErrorOutput();
                    } else {
                        $this->incrementalErrorOutputOffset = ftell($this->stderr);
                    }

                    yield self::ERR => $err;
                }
            }

            if (!$blocking && !isset($out[0]) && !isset($err[0])) {
                yield self::OUT => '';
            }

            $this->checkTimeout();
            $this->readPipesForOutput(__FUNCTION__, $blocking);
        }
    }

    /**
     * Clears the process output.
     *
     * @return $this
     */
    public function clearOutput(): static
    {
        ftruncate($this->stdout, 0);
        fseek($this->stdout, 0);
        $this->incrementalOutputOffset = 0;

        return $this;
    }

    /**
     * Returns the current error output of the process (STDERR).
     *
     * @throws LogicException in case the output has been disabled
     * @throws LogicException In case the process is not started
     */
    public function getErrorOutput(): string
    {
        $this->readPipesForOutput(__FUNCTION__);

        if (false === $ret = stream_get_contents($this->stderr, -1, 0)) {
            return '';
        }

        return $ret;
    }

    /**
     * Returns the errorOutput incrementally.
     *
     * In comparison with the getErrorOutput method which always return the
     * whole error output, this one returns the new error output since the last
     * call.
     *
     * @throws LogicException in case the output has been disabled
     * @throws LogicException In case the process is not started
     */
    public function getIncrementalErrorOutput(): string
    {
        $this->readPipesForOutput(__FUNCTION__);

        $latest = stream_get_contents($this->stderr, -1, $this->incrementalErrorOutputOffset);
        $this->incrementalErrorOutputOffset = ftell($this->stderr);

        if (false === $latest) {
            return '';
        }

        return $latest;
    }

    /**
     * Clears the process output.
     *
     * @return $this
     */
    public function clearErrorOutput(): static
    {
        ftruncate($this->stderr, 0);
        fseek($this->stderr, 0);
        $this->incrementalErrorOutputOffset = 0;

        return $this;
    }

    /**
     * Returns the exit code returned by the process.
     *
     * @return int|null The exit status code, null if the Process is not terminated
     */
    public function getExitCode(): ?int
    {
        $this->updateStatus(false);

        return $this->exitcode;
    }

    /**
     * Returns a string representation for the exit code returned by the process.
     *
     * This method relies on the Unix exit code status standardization
     * and might not be relevant for other operating systems.
     *
     * @return string|null A string representation for the exit status code, null if the Process is not terminated
     *
     * @see http://tldp.org/LDP/abs/html/exitcodes.html
     * @see http://en.wikipedia.org/wiki/Unix_signal
     */
    public function getExitCodeText(): ?string
    {
        if (null === $exitcode = $this->getExitCode()) {
            return null;
        }

        return self::$exitCodes[$exitcode] ?? 'Unknown error';
    }

    /**
     * Checks if the process ended successfully.
     */
    public function isSuccessful(): bool
    {
        return 0 === $this->getExitCode();
    }

    /**
     * Returns true if the child process has been terminated by an uncaught signal.
     *
     * It always returns false on Windows.
     *
     * @throws LogicException In case the process is not terminated
     */
    public function hasBeenSignaled(): bool
    {
        $this->requireProcessIsTerminated(__FUNCTION__);

        return $this->processInformation['signaled'];
    }

    /**
     * Returns the number of the signal that caused the child process to terminate its execution.
     *
     * It is only meaningful if hasBeenSignaled() returns true.
     *
     * @throws RuntimeException In case --enable-sigchild is activated
     * @throws LogicException   In case the process is not terminated
     */
    public function getTermSignal(): int
    {
        $this->requireProcessIsTerminated(__FUNCTION__);

        if ($this->isSigchildEnabled() && -1 === $this->processInformation['termsig']) {
            throw new RuntimeException('This PHP has been compiled with --enable-sigchild. Term signal cannot be retrieved.');
        }

        return $this->processInformation['termsig'];
    }

    /**
     * Returns true if the child process has been stopped by a signal.
     *
     * It always returns false on Windows.
     *
     * @throws LogicException In case the process is not terminated
     */
    public function hasBeenStopped(): bool
    {
        $this->requireProcessIsTerminated(__FUNCTION__);

        return $this->processInformation['stopped'];
    }

    /**
     * Returns the number of the signal that caused the child process to stop its execution.
     *
     * It is only meaningful if hasBeenStopped() returns true.
     *
     * @throws LogicException In case the process is not terminated
     */
    public function getStopSignal(): int
    {
        $this->requireProcessIsTerminated(__FUNCTION__);

        return $this->processInformation['stopsig'];
    }

    /**
     * Checks if the process is currently running.
     */
    public function isRunning(): bool
    {
        if (self::STATUS_STARTED !== $this->status) {
            return false;
        }

        $this->updateStatus(false);

        return $this->processInformation['running'];
    }

    /**
     * Checks if the process has been started with no regard to the current state.
     */
    public function isStarted(): bool
    {
        return self::STATUS_READY != $this->status;
    }

    /**
     * Checks if the process is terminated.
     */
    public function isTerminated(): bool
    {
        $this->updateStatus(false);

        return self::STATUS_TERMINATED == $this->status;
    }

    /**
     * Gets the process status.
     *
     * The status is one of: ready, started, terminated.
     */
    public function getStatus(): string
    {
        $this->updateStatus(false);

        return $this->status;
    }

    /**
     * Stops the process.
     *
     * @param int|float $timeout The timeout in seconds
     * @param int|null  $signal  A POSIX signal to send in case the process has not stop at timeout, default is SIGKILL (9)
     *
     * @return int|null The exit-code of the process or null if it's not running
     */
    public function stop(float $timeout = 10, ?int $signal = null): ?int
    {
        $timeoutMicro = microtime(true) + $timeout;
        if ($this->isRunning()) {
            // given SIGTERM may not be defined and that "proc_terminate" uses the constant value and not the constant itself, we use the same here
            $this->doSignal(15, false);
            do {
                usleep(1000);
            } while ($this->isRunning() && microtime(true) < $timeoutMicro);

            if ($this->isRunning()) {
                // Avoid exception here: process is supposed to be running, but it might have stopped just
                // after this line. In any case, let's silently discard the error, we cannot do anything.
                $this->doSignal($signal ?: 9, false);
            }
        }

        if ($this->isRunning()) {
            if (isset($this->fallbackStatus['pid'])) {
                unset($this->fallbackStatus['pid']);

                return $this->stop(0, $signal);
            }
            $this->close();
        }

        return $this->exitcode;
    }

    /**
     * Adds a line to the STDOUT stream.
     *
     * @internal
     */
    public function addOutput(string $line): void
    {
        $this->lastOutputTime = microtime(true);

        fseek($this->stdout, 0, \SEEK_END);
        fwrite($this->stdout, $line);
        fseek($this->stdout, $this->incrementalOutputOffset);
    }

    /**
     * Adds a line to the STDERR stream.
     *
     * @internal
     */
    public function addErrorOutput(string $line): void
    {
        $this->lastOutputTime = microtime(true);

        fseek($this->stderr, 0, \SEEK_END);
        fwrite($this->stderr, $line);
        fseek($this->stderr, $this->incrementalErrorOutputOffset);
    }

    /**
     * Gets the last output time in seconds.
     */
    public function getLastOutputTime(): ?float
    {
        return $this->lastOutputTime;
    }

    /**
     * Gets the command line to be executed.
     */
    public function getCommandLine(): string
    {
        return $this->buildShellCommandline($this->commandline);
    }

    /**
     * Gets the process timeout in seconds (max. runtime).
     */
    public function getTimeout(): ?float
    {
        return $this->timeout;
    }

    /**
     * Gets the process idle timeout in seconds (max. time since last output).
     */
    public function getIdleTimeout(): ?float
    {
        return $this->idleTimeout;
    }

    /**
     * Sets the process timeout (max. runtime) in seconds.
     *
     * To disable the timeout, set this value to null.
     *
     * @return $this
     *
     * @throws InvalidArgumentException if the timeout is negative
     */
    public function setTimeout(?float $timeout): static
    {
        $this->timeout = $this->validateTimeout($timeout);

        return $this;
    }

    /**
     * Sets the process idle timeout (max. time since last output) in seconds.
     *
     * To disable the timeout, set this value to null.
     *
     * @return $this
     *
     * @throws LogicException           if the output is disabled
     * @throws InvalidArgumentException if the timeout is negative
     */
    public function setIdleTimeout(?float $timeout): static
    {
        if (null !== $timeout && $this->outputDisabled) {
            throw new LogicException('Idle timeout cannot be set while the output is disabled.');
        }

        $this->idleTimeout = $this->validateTimeout($timeout);

        return $this;
    }

    /**
     * Enables or disables the TTY mode.
     *
     * @return $this
     *
     * @throws RuntimeException In case the TTY mode is not supported
     */
    public function setTty(bool $tty): static
    {
        if ('\\' === \DIRECTORY_SEPARATOR && $tty) {
            throw new RuntimeException('TTY mode is not supported on Windows platform.');
        }

        if ($tty && !self::isTtySupported()) {
            throw new RuntimeException('TTY mode requires /dev/tty to be read/writable.');
        }

        $this->tty = $tty;

        return $this;
    }

    /**
     * Checks if the TTY mode is enabled.
     */
    public function isTty(): bool
    {
        return $this->tty;
    }

    /**
     * Sets PTY mode.
     *
     * @return $this
     */
    public function setPty(bool $bool): static
    {
        $this->pty = $bool;

        return $this;
    }

    /**
     * Returns PTY state.
     */
    public function isPty(): bool
    {
        return $this->pty;
    }

    /**
     * Gets the working directory.
     */
    public function getWorkingDirectory(): ?string
    {
        if (null === $this->cwd) {
            // getcwd() will return false if any one of the parent directories does not have
            // the readable or search mode set, even if the current directory does
            return getcwd() ?: null;
        }

        return $this->cwd;
    }

    /**
     * Sets the current working directory.
     *
     * @return $this
     */
    public function setWorkingDirectory(string $cwd): static
    {
        $this->cwd = $cwd;

        return $this;
    }

    /**
     * Gets the environment variables.
     */
    public function getEnv(): array
    {
        return $this->env;
    }

    /**
     * Sets the environment variables.
     *
     * @param array<string|\Stringable> $env The new environment variables
     *
     * @return $this
     */
    public function setEnv(array $env): static
    {
        $this->env = $env;

        return $this;
    }

    /**
     * Gets the Process input.
     *
     * @return resource|string|\Iterator|null
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Sets the input.
     *
     * This content will be passed to the underlying process standard input.
     *
     * @param string|resource|\Traversable|self|null $input The content
     *
     * @return $this
     *
     * @throws LogicException In case the process is running
     */
    public function setInput(mixed $input): static
    {
        if ($this->isRunning()) {
            throw new LogicException('Input cannot be set while the process is running.');
        }

        $this->input = ProcessUtils::validateInput(__METHOD__, $input);

        return $this;
    }

    /**
     * Performs a check between the timeout definition and the time the process started.
     *
     * In case you run a background process (with the start method), you should
     * trigger this method regularly to ensure the process timeout
     *
     * @throws ProcessTimedOutException In case the timeout was reached
     */
    public function checkTimeout(): void
    {
        if (self::STATUS_STARTED !== $this->status) {
            return;
        }

        if (null !== $this->timeout && $this->timeout < microtime(true) - $this->starttime) {
            $this->stop(0);

            throw new ProcessTimedOutException($this, ProcessTimedOutException::TYPE_GENERAL);
        }

        if (null !== $this->idleTimeout && $this->idleTimeout < microtime(true) - $this->lastOutputTime) {
            $this->stop(0);

            throw new ProcessTimedOutException($this, ProcessTimedOutException::TYPE_IDLE);
        }
    }

    /**
     * @throws LogicException in case process is not started
     */
    public function getStartTime(): float
    {
        if (!$this->isStarted()) {
            throw new LogicException('Start time is only available after process start.');
        }

        return $this->starttime;
    }

    /**
     * Defines options to pass to the underlying proc_open().
     *
     * @see https://php.net/proc_open for the options supported by PHP.
     *
     * Enabling the "create_new_console" option allows a subprocess to continue
     * to run after the main process exited, on both Windows and *nix
     */
    public function setOptions(array $options): void
    {
        if ($this->isRunning()) {
            throw new RuntimeException('Setting options while the process is running is not possible.');
        }

        $defaultOptions = $this->options;
        $existingOptions = ['blocking_pipes', 'create_process_group', 'create_new_console'];

        foreach ($options as $key => $value) {
            if (!\in_array($key, $existingOptions)) {
                $this->options = $defaultOptions;
                throw new LogicException(\sprintf('Invalid option "%s" passed to "%s()". Supported options are "%s".', $key, __METHOD__, implode('", "', $existingOptions)));
            }
            $this->options[$key] = $value;
        }
    }

    /**
     * Defines a list of posix signals that will not be propagated to the process.
     *
     * @param list<\SIG*> $signals
     */
    public function setIgnoredSignals(array $signals): void
    {
        if ($this->isRunning()) {
            throw new RuntimeException('Setting ignored signals while the process is running is not possible.');
        }

        $this->ignoredSignals = $signals;
    }

    /**
     * Returns whether TTY is supported on the current operating system.
     */
    public static function isTtySupported(): bool
    {
        static $isTtySupported;

        return $isTtySupported ??= ('/' === \DIRECTORY_SEPARATOR && stream_isatty(\STDOUT) && @is_writable('/dev/tty'));
    }

    /**
     * Returns whether PTY is supported on the current operating system.
     */
    public static function isPtySupported(): bool
    {
        static $result;

        if (null !== $result) {
            return $result;
        }

        if ('\\' === \DIRECTORY_SEPARATOR) {
            return $result = false;
        }

        return $result = (bool) @proc_open('echo 1 >/dev/null', [['pty'], ['pty'], ['pty']], $pipes);
    }

    /**
     * Creates the descriptors needed by the proc_open.
     */
    private function getDescriptors(bool $hasCallback): array
    {
        if ($this->input instanceof \Iterator) {
            $this->input->rewind();
        }
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $this->processPipes = new WindowsPipes($this->input, !$this->outputDisabled || $hasCallback);
        } else {
            $this->processPipes = new UnixPipes($this->isTty(), $this->isPty(), $this->input, !$this->outputDisabled || $hasCallback);
        }

        return $this->processPipes->getDescriptors();
    }

    /**
     * Builds up the callback used by wait().
     *
     * The callbacks adds all occurred output to the specific buffer and calls
     * the user callback (if present) with the received output.
     *
     * @param callable('out'|'err', string)|null $callback
     *
     * @return \Closure('out'|'err', string):bool
     */
    protected function buildCallback(?callable $callback = null): \Closure
    {
        if ($this->outputDisabled) {
            return static fn ($type, $data): bool => null !== $callback && $callback($type, $data);
        }

        return function ($type, $data) use ($callback): bool {
            match ($type) {
                self::OUT => $this->addOutput($data),
                self::ERR => $this->addErrorOutput($data),
            };

            return null !== $callback && $callback($type, $data);
        };
    }

    /**
     * Updates the status of the process, reads pipes.
     *
     * @param bool $blocking Whether to use a blocking read call
     */
    protected function updateStatus(bool $blocking): void
    {
        if (self::STATUS_STARTED !== $this->status) {
            return;
        }

        if ($this->processInformation['running'] ?? true) {
            $this->processInformation = proc_get_status($this->process);
        }
        $running = $this->processInformation['running'];

        $this->readPipes($running && $blocking, '\\' !== \DIRECTORY_SEPARATOR || !$running);

        if ($this->fallbackStatus && $this->isSigchildEnabled()) {
            $this->processInformation = $this->fallbackStatus + $this->processInformation;
        }

        if (!$running) {
            $this->close();
        }
    }

    /**
     * Returns whether PHP has been compiled with the '--enable-sigchild' option or not.
     */
    protected function isSigchildEnabled(): bool
    {
        if (null !== self::$sigchild) {
            return self::$sigchild;
        }

        if (!\function_exists('phpinfo')) {
            return self::$sigchild = false;
        }

        ob_start();
        phpinfo(\INFO_GENERAL);

        return self::$sigchild = str_contains(ob_get_clean(), '--enable-sigchild');
    }

    /**
     * Reads pipes for the freshest output.
     *
     * @param string $caller   The name of the method that needs fresh outputs
     * @param bool   $blocking Whether to use blocking calls or not
     *
     * @throws LogicException in case output has been disabled or process is not started
     */
    private function readPipesForOutput(string $caller, bool $blocking = false): void
    {
        if ($this->outputDisabled) {
            throw new LogicException('Output has been disabled.');
        }

        $this->requireProcessIsStarted($caller);

        $this->updateStatus($blocking);
    }

    /**
     * Validates and returns the filtered timeout.
     *
     * @throws InvalidArgumentException if the given timeout is a negative number
     */
    private function validateTimeout(?float $timeout): ?float
    {
        $timeout = (float) $timeout;

        if (0.0 === $timeout) {
            $timeout = null;
        } elseif ($timeout < 0) {
            throw new InvalidArgumentException('The timeout value must be a valid positive integer or float number.');
        }

        return $timeout;
    }

    /**
     * Reads pipes, executes callback.
     *
     * @param bool $blocking Whether to use blocking calls or not
     * @param bool $close    Whether to close file handles or not
     */
    private function readPipes(bool $blocking, bool $close): void
    {
        $result = $this->processPipes->readAndWrite($blocking, $close);

        $callback = $this->callback;
        foreach ($result as $type => $data) {
            if (3 !== $type) {
                $callback(self::STDOUT === $type ? self::OUT : self::ERR, $data);
            } elseif (!isset($this->fallbackStatus['signaled'])) {
                $this->fallbackStatus['exitcode'] = (int) $data;
            }
        }
    }

    /**
     * Closes process resource, closes file handles, sets the exitcode.
     *
     * @return int The exitcode
     */
    private function close(): int
    {
        $this->processPipes->close();
        if ($this->process) {
            proc_close($this->process);
            $this->process = null;
        }
        $this->exitcode = $this->processInformation['exitcode'];
        $this->status = self::STATUS_TERMINATED;

        if (-1 === $this->exitcode) {
            if ($this->processInformation['signaled'] && 0 < $this->processInformation['termsig']) {
                // if process has been signaled, no exitcode but a valid termsig, apply Unix convention
                $this->exitcode = 128 + $this->processInformation['termsig'];
            } elseif ($this->isSigchildEnabled()) {
                $this->processInformation['signaled'] = true;
                $this->processInformation['termsig'] = -1;
            }
        }

        // Free memory from self-reference callback created by buildCallback
        // Doing so in other contexts like __destruct or by garbage collector is ineffective
        // Now pipes are closed, so the callback is no longer necessary
        $this->callback = null;

        return $this->exitcode;
    }

    /**
     * Resets data related to the latest run of the process.
     */
    private function resetProcessData(): void
    {
        $this->starttime = null;
        $this->callback = null;
        $this->exitcode = null;
        $this->fallbackStatus = [];
        $this->processInformation = [];
        $this->stdout = fopen('php://temp/maxmemory:'.(1024 * 1024), 'w+');
        $this->stderr = fopen('php://temp/maxmemory:'.(1024 * 1024), 'w+');
        $this->process = null;
        $this->latestSignal = null;
        $this->status = self::STATUS_READY;
        $this->incrementalOutputOffset = 0;
        $this->incrementalErrorOutputOffset = 0;
    }

    /**
     * Sends a POSIX signal to the process.
     *
     * @param int  $signal         A valid POSIX signal (see https://php.net/pcntl.constants)
     * @param bool $throwException Whether to throw exception in case signal failed
     *
     * @throws LogicException   In case the process is not running
     * @throws RuntimeException In case --enable-sigchild is activated and the process can't be killed
     * @throws RuntimeException In case of failure
     */
    private function doSignal(int $signal, bool $throwException): bool
    {
        // Signal seems to be send when sigchild is enable, this allow blocking the signal correctly in this case
        if ($this->isSigchildEnabled() && \in_array($signal, $this->ignoredSignals)) {
            return false;
        }

        if (null === $pid = $this->getPid()) {
            if ($throwException) {
                throw new LogicException('Cannot send signal on a non running process.');
            }

            return false;
        }

        if ('\\' === \DIRECTORY_SEPARATOR) {
            exec(\sprintf('taskkill /F /T /PID %d 2>&1', $pid), $output, $exitCode);
            if ($exitCode && $this->isRunning()) {
                if ($throwException) {
                    throw new RuntimeException(\sprintf('Unable to kill the process (%s).', implode(' ', $output)));
                }

                return false;
            }
        } else {
            if (!$this->isSigchildEnabled()) {
                $ok = @proc_terminate($this->process, $signal);
            } elseif (\function_exists('posix_kill')) {
                $ok = @posix_kill($pid, $signal);
            } elseif ($ok = proc_open(\sprintf('kill -%d %d', $signal, $pid), [2 => ['pipe', 'w']], $pipes)) {
                $ok = false === fgets($pipes[2]);
            }
            if (!$ok) {
                if ($throwException) {
                    throw new RuntimeException(\sprintf('Error while sending signal "%s".', $signal));
                }

                return false;
            }
        }

        $this->latestSignal = $signal;
        $this->fallbackStatus['signaled'] = true;
        $this->fallbackStatus['exitcode'] = -1;
        $this->fallbackStatus['termsig'] = $this->latestSignal;

        return true;
    }

    private function buildShellCommandline(string|array $commandline): string
    {
        if (\is_string($commandline)) {
            return $commandline;
        }

        if ('\\' === \DIRECTORY_SEPARATOR && isset($commandline[0][0]) && \strlen($commandline[0]) === strcspn($commandline[0], ':/\\')) {
            // On Windows, we don't rely on the OS to find the executable if possible to avoid lookups
            // in the current directory which could be untrusted. Instead we use the ExecutableFinder.
            $commandline[0] = (self::$executables[$commandline[0]] ??= (new ExecutableFinder())->find($commandline[0])) ?? $commandline[0];
        }

        return implode(' ', array_map($this->escapeArgument(...), $commandline));
    }

    private function prepareWindowsCommandLine(string|array $cmd, array &$env): string
    {
        $cmd = $this->buildShellCommandline($cmd);
        $uid = bin2hex(random_bytes(4));
        $cmd = preg_replace_callback(
            '/"(?:(
                [^"%!^]*+
                (?:
                    (?: !LF! | "(?:\^[%!^])?+" )
                    [^"%!^]*+
                )++
            ) | [^"]*+ )"/x',
            static function ($m) use (&$env, $uid) {
                static $varCount = 0;
                static $varCache = [];
                if (!isset($m[1])) {
                    return $m[0];
                }
                if (isset($varCache[$m[0]])) {
                    return $varCache[$m[0]];
                }
                if (str_contains($value = $m[1], "\0")) {
                    $value = str_replace("\0", '?', $value);
                }
                if (false === strpbrk($value, "\"%!\n")) {
                    return '"'.$value.'"';
                }

                $value = str_replace(['!LF!', '"^!"', '"^%"', '"^^"', '""'], ["\n", '!', '%', '^', '"'], $value);
                $value = '"'.preg_replace('/(\\\\*)"/', '$1$1\\"', $value).'"';
                $var = $uid.++$varCount;

                $env[$var] = $value;

                return $varCache[$m[0]] = '!'.$var.'!';
            },
            $cmd
        );

        static $comSpec;

        if (!$comSpec && $comSpec = (new ExecutableFinder())->find('cmd.exe')) {
            // Escape according to CommandLineToArgvW rules
            $comSpec = '"'.preg_replace('{(\\\\*+)"}', '$1$1\"', $comSpec).'"';
        }

        $cmd = ($comSpec ?? 'cmd').' /V:ON /E:ON /D /C ('.str_replace("\n", ' ', $cmd).')';
        foreach ($this->processPipes->getFiles() as $offset => $filename) {
            $cmd .= ' '.$offset.'>"'.$filename.'"';
        }

        return $cmd;
    }

    /**
     * Ensures the process is running or terminated, throws a LogicException if the process has a not started.
     *
     * @throws LogicException if the process has not run
     */
    private function requireProcessIsStarted(string $functionName): void
    {
        if (!$this->isStarted()) {
            throw new LogicException(\sprintf('Process must be started before calling "%s()".', $functionName));
        }
    }

    /**
     * Ensures the process is terminated, throws a LogicException if the process has a status different than "terminated".
     *
     * @throws LogicException if the process is not yet terminated
     */
    private function requireProcessIsTerminated(string $functionName): void
    {
        if (!$this->isTerminated()) {
            throw new LogicException(\sprintf('Process must be terminated before calling "%s()".', $functionName));
        }
    }

    /**
     * Escapes a string to be used as a shell argument.
     */
    private function escapeArgument(?string $argument): string
    {
        if ('' === $argument || null === $argument) {
            return '""';
        }
        if ('\\' !== \DIRECTORY_SEPARATOR) {
            return "'".str_replace("'", "'\\''", $argument)."'";
        }
        if (str_contains($argument, "\0")) {
            $argument = str_replace("\0", '?', $argument);
        }
        if (!preg_match('/[()%!^"<>&|\s[\]=;*?\'$]/', $argument)) {
            return $argument;
        }
        $argument = preg_replace('/(\\\\+)$/', '$1$1', $argument);

        return '"'.str_replace(['"', '^', '%', '!', "\n"], ['""', '"^^"', '"^%"', '"^!"', '!LF!'], $argument).'"';
    }

    private function replacePlaceholders(string $commandline, array $env): string
    {
        return preg_replace_callback('/"\$\{:([_a-zA-Z]++[_a-zA-Z0-9]*+)\}"/', function ($matches) use ($commandline, $env) {
            if (!isset($env[$matches[1]]) || false === $env[$matches[1]]) {
                throw new InvalidArgumentException(\sprintf('Command line is missing a value for parameter "%s": ', $matches[1]).$commandline);
            }

            return $this->escapeArgument($env[$matches[1]]);
        }, $commandline);
    }

    private function getDefaultEnv(): array
    {
        $env = getenv();
        $env = ('\\' === \DIRECTORY_SEPARATOR ? array_intersect_ukey($env, $_SERVER, 'strcasecmp') : array_intersect_key($env, $_SERVER)) ?: $env;
        $env = $_ENV + ('\\' === \DIRECTORY_SEPARATOR ? array_diff_ukey($env, $_ENV, 'strcasecmp') : $env);

        if (\in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true)) {
            return $env;
        }

        // On non-CLI SAPIs (notably PHP-FPM and CGI), CGI/FastCGI request-context
        // vars are exposed through $_SERVER, $_ENV and getenv(), and must not
        // propagate to subprocesses.
        foreach ($env as $k => $v) {
            if (str_starts_with($k, 'HTTP_')
                || str_starts_with($k, 'ORIG_')
                || str_starts_with($k, 'REDIRECT_')
                || \in_array($k, [
                    'AUTH_TYPE', 'CONTENT_LENGTH', 'CONTENT_TYPE', 'DOCUMENT_ROOT',
                    'DOCUMENT_URI', 'GATEWAY_INTERFACE', 'HTTPS', 'PATH_INFO',
                    'PATH_TRANSLATED', 'PHP_AUTH_DIGEST', 'PHP_AUTH_PW', 'PHP_AUTH_USER',
                    'PHP_SELF', 'QUERY_STRING', 'REMOTE_ADDR', 'REMOTE_HOST',
                    'REMOTE_IDENT', 'REMOTE_PORT', 'REMOTE_USER', 'REQUEST_METHOD',
                    'REQUEST_SCHEME', 'REQUEST_TIME', 'REQUEST_TIME_FLOAT', 'REQUEST_URI',
                    'SCRIPT_FILENAME', 'SCRIPT_NAME', 'SCRIPT_URI', 'SCRIPT_URL',
                    'SERVER_ADDR', 'SERVER_ADMIN', 'SERVER_NAME', 'SERVER_PORT',
                    'SERVER_PROTOCOL', 'SERVER_SIGNATURE', 'SERVER_SOFTWARE',
                ], true)
            ) {
                unset($env[$k]);
            }
        }

        return $env;
    }

    private function validateWindowsEnvBlockSize(array $envPairs): void
    {
        $block = implode("\0", $envPairs)."\0";
        @preg_replace('/./u', '', $block, -1, $blockLength)
            ?? preg_replace('/./', '', $block, -1, $blockLength);
        $blockLength += 1 + preg_match_all('/[\xF0-\xF4][\x80-\xBF]{3}/', $block);

        if ($blockLength > self::WINDOWS_ENV_BLOCK_MAX_LENGTH) {
            throw new InvalidArgumentException(\sprintf('The environment block size (%d) exceeds the Windows limit of %d UTF-16 code units.', $blockLength, self::WINDOWS_ENV_BLOCK_MAX_LENGTH));
        }
    }
}
