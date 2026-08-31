<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use Nette;
use function is_resource, is_string, strlen;
use const PHP_VERSION_ID;


/**
 * Represents a process, which can be started and controlled (reading output, writing input, waiting for completion).
 */
final class Process
{
	private const PollInterval = 10_000;
	private const DefaultTimeout = 60;
	private const StdIn = 0;
	private const StdOut = 1;
	private const StdErr = 2;

	/** @var resource */
	private mixed $process;

	/** @var array<string, mixed>  result of proc_get_status() */
	private array $status = ['running' => true];

	/** @var resource */
	private mixed $inputPipe;

	/** @var array<int, resource|string[]>  pipe resources, or ['file', path, mode] descriptors for discarded output */
	private array $outputPipes = [];

	/** @var string[] */
	private array $outputBuffers = [];

	/** @var int[] Number of bytes already read from buffers. */
	private array $outputBufferOffsets = [];

	/** @var array<int, true> Output IDs whose target resource was supplied by the caller and must not be closed here. */
	private array $callerOutputs = [];

	/** @var array<int, true> Output IDs whose pipe is backed by a temporary file (Windows < 8.5 workaround). */
	private array $fileBackedOutputs = [];
	private float $startTime;
	private bool $detached = false;


	/**
	 * Starts an executable with given arguments. Because the arguments are passed as an array, the shell is
	 * never involved, so they need no escaping and there is no risk of shell injection.
	 * @param  string         $executable Path to the executable binary.
	 * @param  list<string>   $arguments  Arguments passed to the executable.
	 * @param  string[]|null  $env        Environment variables or null to use the same environment as the current process.
	 * @param  array<string, mixed>  $options  Additional options for proc_open(). On Windows the executable is launched directly, without cmd.exe.
	 * @param  mixed          $stdin      Input: string, a readable resource (its content is copied to STDIN), a Process (its STDOUT is piped in), or null (STDIN stays open for writeStdInput()).
	 * @param  mixed          $stdout     Output target: string filename, a writable resource backed by a real OS file descriptor (not php://memory etc.), false to discard, or null to capture into memory.
	 * @param  mixed          $stderr     Error output target (same options as $stdout).
	 * @param  string|null    $directory  Working directory.
	 * @param  float|null     $timeout    Time limit in seconds, checked while waiting for or reading the process; null disables it.
	 */
	public static function runExecutable(
		string $executable,
		array $arguments = [],
		?array $env = null,
		array $options = [],
		mixed $stdin = '',
		mixed $stdout = null,
		mixed $stderr = null,
		?string $directory = null,
		?float $timeout = self::DefaultTimeout,
	): self
	{
		return new self([$executable, ...$arguments], $env, $options, $directory, $stdin, $stdout, $stderr, $timeout);
	}


	/**
	 * Starts a process from a command string interpreted by the shell (/bin/sh on POSIX, cmd.exe on Windows).
	 * Because the shell parses the string, NEVER pass unescaped user input here - use runExecutable() for that.
	 * @param  string         $command    Shell command to run.
	 * @param  string[]|null  $env        Environment variables or null to use the same environment as the current process.
	 * @param  array<string, mixed>  $options  Options for proc_open(), e.g. ['bypass_shell' => true] on Windows to skip cmd.exe.
	 * @param  mixed          $stdin      Input: string, a readable resource (its content is copied to STDIN), a Process (its STDOUT is piped in), or null (STDIN stays open for writeStdInput()).
	 * @param  mixed          $stdout     Output target: string filename, a writable resource backed by a real OS file descriptor (not php://memory etc.), false to discard, or null to capture into memory.
	 * @param  mixed          $stderr     Error output target (same options as $stdout).
	 * @param  string|null    $directory  Working directory.
	 * @param  float|null     $timeout    Time limit in seconds, checked while waiting for or reading the process; null disables it.
	 */
	public static function runCommand(
		string $command,
		?array $env = null,
		array $options = [],
		mixed $stdin = '',
		mixed $stdout = null,
		mixed $stderr = null,
		?string $directory = null,
		?float $timeout = self::DefaultTimeout,
	): self
	{
		return new self($command, $env, $options, $directory, $stdin, $stdout, $stderr, $timeout);
	}


	/**
	 * @param  list<string>|string  $command
	 * @param  array<string, string>|null  $env
	 * @param  array<string, mixed>  $options
	 */
	private function __construct(
		string|array $command,
		?array $env,
		array $options,
		?string $directory,
		mixed $stdin,
		mixed $stdout,
		mixed $stderr,
		private readonly ?float $timeout,
	) {
		$descriptors = [
			self::StdIn => $this->createInputDescriptor($stdin),
			self::StdOut => $this->createOutputDescriptor(self::StdOut, $stdout),
			self::StdErr => $this->createOutputDescriptor(self::StdErr, $stderr),
		];

		$process = @proc_open($command, $descriptors, $pipes, $directory, $env, $options);
		if (!is_resource($process)) {
			throw new ProcessFailedException('Failed to start process: ' . Helpers::getLastError());
		}

		$this->process = $process;
		[$this->inputPipe, $this->outputPipes[self::StdOut], $this->outputPipes[self::StdErr]] = $pipes + $descriptors;

		if ($stdin instanceof self) {
			// the source process hands over its STDOUT pipe; from now on this process owns it
			unset(
				$stdin->outputBuffers[self::StdOut],
				$stdin->outputBufferOffsets[self::StdOut],
				$stdin->outputPipes[self::StdOut],
			);
		}

		$this->writeInitialInput($stdin);
		$this->startTime = microtime(true);
	}


	public function __destruct()
	{
		if ($this->detached) {
			return;
		}
		$this->outputBuffers = [];
		$this->terminate();
	}


	/**
	 * Detaches the process: it keeps running in the background and is not terminated when the object
	 * is destroyed. STDIN and output pipes are closed, so the output must not be captured in memory
	 * (pass a file name, a resource or false as $stdout/$stderr). Only the destructor behavior changes:
	 * wait() and getExitCode() still block ($timeout still applies) and terminate() still kills it.
	 * On POSIX, a detached child that exits early stays a zombie until the script ends.
	 */
	public function detach(): void
	{
		if ($this->outputBuffers !== []) {
			throw new Nette\InvalidStateException('Cannot detach process: its output is captured in memory, pass a file name, a resource or false as $stdout/$stderr.');
		}
		$this->detached = true;
		$this->closeStdInput();
		$this->closeOutputPipes();
	}


	/**
	 * Checks if the process is currently running.
	 */
	public function isRunning(): bool
	{
		if (!$this->status['running']) {
			return false;
		}

		$this->status = proc_get_status($this->process);
		if (!$this->status['running']) {
			$this->close();
		}

		return $this->status['running'];
	}


	/**
	 * Finishes the process by waiting for its completion. While waiting, the captured output is read
	 * continuously and kept in memory; an optional callback is invoked with each new output/error chunk.
	 *
	 * @param  (\Closure(string, string): void)|null  $callback
	 */
	public function wait(?\Closure $callback = null): void
	{
		while ($this->isRunning()) {
			$this->enforceTimeout();
			$this->drainPipes();
			$this->dispatchCallback($callback);
			usleep(self::PollInterval);
		}

		$this->dispatchCallback($callback);
	}


	/**
	 * Reads any new data from the captured pipes into the buffers, so a process producing more output
	 * than the OS pipe buffer holds does not block. With $final (process already finished) reads to EOF.
	 */
	private function drainPipes(bool $final = false): void
	{
		foreach ([self::StdOut, self::StdErr] as $id) {
			$this->readFromPipe($id, $final);
		}
	}


	/**
	 * Terminates the running process if it is still running.
	 */
	public function terminate(): void
	{
		if (!$this->isRunning()) {
			return;
		} elseif (Helpers::IsWindows) {
			exec("taskkill /F /T /PID {$this->getPid()} 2>&1");
		} else {
			proc_terminate($this->process, 9); // 9 = SIGKILL: cannot be trapped, so the following proc_close() won't hang
		}
		$this->status['running'] = false;
		$this->close();
	}


	/**
	 * Returns the process exit code. If the process is still running, waits until it finishes.
	 */
	public function getExitCode(): int
	{
		$this->wait();
		return $this->status['exitcode'] ?? -1;
	}


	/**
	 * Returns true if the process terminated with exit code 0.
	 */
	public function isSuccess(): bool
	{
		return $this->getExitCode() === 0;
	}


	/**
	 * Waits for the process to finish and throws ProcessFailedException if exit code is not zero.
	 */
	public function ensureSuccess(): void
	{
		$code = $this->getExitCode();
		if ($code !== 0) {
			throw new ProcessFailedException("Process failed with non-zero exit code: $code");
		}
	}


	/**
	 * Returns the PID of the running process, or null if it is not running.
	 */
	public function getPid(): ?int
	{
		return $this->isRunning() ? $this->status['pid'] : null;
	}


	/**
	 * Waits for the process to finish and returns everything it wrote to STDOUT.
	 */
	public function getStdOutput(): string
	{
		$this->wait();
		return $this->outputBuffers[self::StdOut] ?? throw new Nette\InvalidStateException('Cannot read output: it is not captured (it was redirected, discarded or piped).');
	}


	/**
	 * Waits for the process to finish and returns everything it wrote to STDERR.
	 */
	public function getStdError(): string
	{
		$this->wait();
		return $this->outputBuffers[self::StdErr] ?? throw new Nette\InvalidStateException('Cannot read output: it is not captured (it was redirected, discarded or piped).');
	}


	/**
	 * Returns the STDOUT data produced since the previous consumeStdOutput() call.
	 * To read everything incrementally, poll `while ($p->isRunning())` calling this, then call it once more
	 * after the loop; that last call returns whatever the process wrote just before the loop noticed it had exited.
	 */
	public function consumeStdOutput(): string
	{
		return $this->consumeBuffer(self::StdOut);
	}


	/**
	 * Returns the STDERR data produced since the previous consumeStdError() call. See consumeStdOutput().
	 */
	public function consumeStdError(): string
	{
		return $this->consumeBuffer(self::StdErr);
	}


	/**
	 * Returns newly available data from the specified buffer and advances the read pointer.
	 */
	private function consumeBuffer(int $id): string
	{
		if (!isset($this->outputBuffers[$id])) {
			throw new Nette\InvalidStateException('Cannot read output: it is not captured (it was redirected, discarded or piped).');
		} elseif ($this->isRunning()) {
			$this->enforceTimeout();
			$this->readFromPipe($id);
		}
		return $this->extractNewData($id);
	}


	/**
	 * Returns the buffered data not returned yet and advances the read pointer.
	 */
	private function extractNewData(int $id): string
	{
		if (!isset($this->outputBuffers[$id])) {
			return '';
		}
		$res = substr($this->outputBuffers[$id], $this->outputBufferOffsets[$id]);
		$this->outputBufferOffsets[$id] = strlen($this->outputBuffers[$id]);
		return $res;
	}


	/**
	 * Writes data into the process' STDIN. If STDIN is closed, throws exception.
	 */
	public function writeStdInput(string $string): void
	{
		if (!is_resource($this->inputPipe)) {
			throw new Nette\InvalidStateException('Cannot write to process: STDIN pipe is closed');
		}
		$this->writeToPipe($string);
	}


	/**
	 * Writes the whole string to STDIN, handling partial writes. Stops (and lets fwrite() warn) on a broken pipe,
	 * i.e. when the process stopped reading its STDIN.
	 */
	private function writeToPipe(string $string): void
	{
		$length = strlen($string);
		for ($written = 0; $written < $length; $written += $bytes) {
			$bytes = fwrite($this->inputPipe, substr($string, $written));
			if (!$bytes) {
				break;
			}
		}
	}


	/**
	 * Closes the STDIN pipe, indicating no more data will be sent.
	 */
	public function closeStdInput(): void
	{
		if (is_resource($this->inputPipe)) {
			fclose($this->inputPipe);
		}
	}


	/**
	 * If a callback is given, invokes it with the output/error produced since the previous call.
	 * @param  (\Closure(string, string): void)|null  $callback
	 */
	private function dispatchCallback(?\Closure $callback): void
	{
		if (!$callback) {
			return;
		}
		$output = $this->extractNewData(self::StdOut);
		$error = $this->extractNewData(self::StdErr);
		if ($output !== '' || $error !== '') {
			$callback($output, $error);
		}
	}


	/**
	 * Checks if the timeout has expired. If yes, terminates the process.
	 */
	private function enforceTimeout(): void
	{
		if ($this->timeout !== null && (microtime(true) - $this->startTime) >= $this->timeout) {
			$this->terminate();
			throw new ProcessTimeoutException('Process exceeded the time limit of ' . $this->timeout . ' seconds');
		}
	}


	/**
	 * Reads any new data from the specified pipe and appends it to the buffer. Does nothing if the output
	 * is not captured or the pipe is already closed (or handed over to another process).
	 */
	private function readFromPipe(int $id, bool $final = false): void
	{
		$pipe = $this->outputPipes[$id] ?? null;
		if (!isset($this->outputBuffers[$id]) || !is_resource($pipe)) {
			return;

		} elseif (isset($this->fileBackedOutputs[$id])) {
			// Windows < 8.5: captured output is a temporary file; read whatever was appended since last time
			fseek($pipe, strlen($this->outputBuffers[$id]));
			$this->outputBuffers[$id] .= stream_get_contents($pipe);

		} elseif ($final) {
			// the process has finished, everything is buffered in the pipe; non-blocking read on POSIX
			// (cannot hang on a leaked descendant still holding the write end), blocking read to EOF on
			// Windows, where non-blocking mode does not work and stream_select() may miss buffered data
			stream_set_blocking($pipe, Helpers::IsWindows);
			$this->outputBuffers[$id] .= stream_get_contents($pipe);

		} else {
			// non-blocking drain: stream_set_blocking(false) works on POSIX only, so reads are also
			// guarded by stream_select(), which works on Windows pipes since PHP 8.5 (PeekNamedPipe fix)
			stream_set_blocking($pipe, false);
			$read = [$pipe];
			$write = $except = [];
			while (@stream_select($read, $write, $except, 0, 0) > 0) {
				$chunk = fread($pipe, 8192);
				if ($chunk === false || $chunk === '') {
					break;
				}
				$this->outputBuffers[$id] .= $chunk;
				$read = [$pipe];
			}
		}
	}


	/**
	 * Sends the initial input to the process: writes and closes a string or stream input,
	 * or leaves STDIN open when input is null (until closeStdInput()) or another Process (fed by that process).
	 * The input type was already validated by createInputDescriptor().
	 *
	 * Note: a string or stream input is written upfront, so if it is large and the process does not read it
	 * while filling its own output, this can block; in that case pass null and feed STDIN via writeStdInput().
	 */
	private function writeInitialInput(mixed $input): void
	{
		if ($input === null || $input instanceof self) {
			// STDIN stays open

		} elseif (is_string($input)) {
			$this->writeToPipe($input);
			$this->closeStdInput();

		} elseif (is_resource($input)) {
			stream_copy_to_stream($input, $this->inputPipe);
			$this->closeStdInput();
		}
	}


	/**
	 * Validates the input and determines the STDIN descriptor based on its type.
	 */
	private function createInputDescriptor(mixed $input): mixed
	{
		if ($input === null || is_string($input) || is_resource($input)) {
			return ['pipe', 'r'];
		} elseif (!$input instanceof self) {
			throw new Nette\InvalidArgumentException('Input must be string, resource, Process or null, ' . get_debug_type($input) . ' given.');
		} elseif (Helpers::IsWindows) {
			throw new Nette\NotSupportedException('Process piping is not supported on Windows.');
		} elseif (!isset($input->outputBuffers[self::StdOut]) || !is_resource($input->outputPipes[self::StdOut] ?? null)) {
			throw new Nette\InvalidStateException('Cannot pipe from the given process: its STDOUT must be captured (it must not be redirected elsewhere).');
		}
		return $input->outputPipes[self::StdOut];
	}


	/**
	 * Determines the descriptor for STDOUT or STDERR based on the specified output target.
	 */
	private function createOutputDescriptor(int $id, mixed $output): mixed
	{
		if (is_resource($output)) {
			$this->callerOutputs[$id] = true;
			return $output;

		} elseif (is_string($output)) {
			return FileSystem::open($output, 'w');

		} elseif ($output === false) {
			return ['file', Helpers::IsWindows ? 'NUL' : '/dev/null', 'w'];

		} elseif ($output === null) {
			$this->outputBuffers[$id] = '';
			$this->outputBufferOffsets[$id] = 0;
			if (Helpers::IsWindows && PHP_VERSION_ID < 80500) {
				// Windows < 8.5: stream_select() doesn't work on pipes, capture into a temp file that reads non-blockingly
				$this->fileBackedOutputs[$id] = true;
				return tmpfile();
			}
			return ['pipe', 'w'];

		} else {
			throw new Nette\InvalidArgumentException('Output must be string, resource, bool or null, ' . get_debug_type($output) . ' given.');
		}
	}


	/**
	 * Closes all pipes and the process resource.
	 */
	private function close(): void
	{
		$this->drainPipes(final: true);
		$this->closeStdInput();
		$this->closeOutputPipes();
		proc_close($this->process);
	}


	/**
	 * Closes the output pipes that this class opened; resources supplied by the caller are left untouched.
	 * (The temporary file backing captured output on Windows < 8.5 is removed by fclose() itself.)
	 */
	private function closeOutputPipes(): void
	{
		foreach ($this->outputPipes as $id => $pipe) {
			if (is_resource($pipe) && !isset($this->callerOutputs[$id])) {
				fclose($pipe);
			}
		}
	}
}
