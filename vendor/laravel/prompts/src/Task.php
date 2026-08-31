<?php

namespace Laravel\Prompts;

use Closure;
use Laravel\Prompts\Support\Logger;
use Laravel\Prompts\Themes\Default\Concerns\InteractsWithStrings;
use RuntimeException;

class Task extends Prompt
{
    use InteractsWithStrings;

    /**
     * The minimum width for the longest line calculation.
     */
    protected int $minWidth = 0;

    /**
     * How long to wait between rendering each frame.
     */
    public int $interval = 100;

    /**
     * The number of times the task has been rendered.
     */
    public int $count = 0;

    /**
     * Whether the task can only be rendered once.
     */
    public bool $static = false;

    /**
     * The process ID after forking.
     */
    protected int $pid;

    /**
     * The socket for IPC communication.
     *
     * @var resource|null
     */
    protected $socket;

    /**
     * Pre-wrapped log lines for the scrolling output area.
     *
     * @var array<int, string>
     */
    public array $logs = [];

    /**
     * Stable status messages (success, warning, error).
     *
     * @var list<array{type: string, message: string}>
     */
    public array $stableMessages = [];

    /**
     * The maximum number of stable messages to display.
     */
    public int $maxStableMessages = 10;

    /**
     * The identifier for the task.
     */
    public string $identifier = '';

    /**
     * Whether the task has finished.
     */
    public bool $finished = false;

    /**
     * Buffer for incomplete lines from non-blocking socket reads.
     */
    protected string $buffer = '';

    /**
     * The log index where the current partial started, or null if not streaming.
     */
    protected ?int $partialStartIndex = null;

    /**
     * Create a new Task instance.
     */
    public function __construct(
        public string $label = '',
        public int $limit = 10,
        public bool $keepSummary = false,
        public ?string $subLabel = null,
    ) {
        $this->identifier = uniqid();
    }

    /**
     * Render the task and execute the callback.
     *
     * @template TReturn of mixed
     *
     * @param  Closure(Logger): TReturn  $callback
     * @return TReturn
     */
    public function run(Closure $callback): mixed
    {
        $this->limit = min($this->limit, $this->terminal()->lines() - 10);
        $this->recalculateMaxStableMessages();

        $this->capturePreviousNewLines();

        if (! static::output()->isDecorated() || ! (function_exists('pcntl_fork') && function_exists('posix_kill'))) {
            return $this->renderStatically($callback);
        }

        $originalAsync = pcntl_async_signals(true);

        pcntl_signal(SIGINT, fn () => exit());

        try {
            $this->hideCursor();
            $this->render();

            $sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);

            if ($sockets === false) {
                return $this->renderStatically($callback);
            }

            $this->pid = pcntl_fork();

            if ($this->pid === 0) {
                fclose($sockets[1]);
                $childSocket = $sockets[0];
                stream_set_blocking($childSocket, false);

                while (true) { // @phpstan-ignore-line
                    $this->receiveMessages($childSocket);

                    if (! $this->finished) {
                        $this->render();
                        $this->count++;
                    }

                    usleep($this->interval * 1000);
                }
            } else {
                fclose($sockets[0]);
                $this->socket = $sockets[1];

                $logger = new Logger($this->identifier, $this->socket);
                $result = $callback($logger);

                if ($this->socket !== null) {
                    // Send a reset message to the parent process to reset the terminal.
                    fwrite($this->socket, $this->identifier.'_'.'reset:'.($originalAsync ? 1 : 0).PHP_EOL);
                    usleep($this->interval * 2000);
                }

                return $result;
            }
        } catch (\Throwable $e) {
            $this->resetTerminal($originalAsync, success: false);

            throw $e;
        }
    }

    /**
     * Receive and process messages from the parent process.
     *
     * @param  resource  $socket
     */
    protected function receiveMessages($socket): void
    {
        $prefix = preg_quote($this->identifier, '/');

        while (($data = fgets($socket)) !== false) {
            // Buffer incomplete lines from non-blocking reads.
            if (! str_ends_with($data, PHP_EOL)) {
                $this->buffer .= $data;

                continue;
            }

            $line = rtrim($this->buffer.$data, PHP_EOL);
            $this->buffer = '';

            if ($line === '') {
                continue;
            }

            // Check for typed messages: {id}_{type}:{content}
            if (preg_match('/^'.$prefix.'_(success|warning|error|label|sublabel|reset|partial|commitpartial):(.*)/', $line, $matches)) {
                $type = $matches[1];
                $content = $matches[2];

                if ($type === 'reset') {
                    $this->resetTerminal((bool) $content);

                    continue;
                }

                if ($type === 'partial') {
                    $this->replacePartialLines($content);

                    continue;
                }

                if ($type === 'commitpartial') {
                    $this->partialStartIndex = null;

                    continue;
                }

                if ($type === 'label') {
                    $this->label = $content;
                } elseif ($type === 'sublabel') {
                    $this->subLabel = $content;
                    $this->recalculateMaxStableMessages();
                } else {
                    $this->stableMessages[] = ['type' => $type, 'message' => $content];
                    $this->logs = [];
                    $this->partialStartIndex = null;
                }

                while (count($this->stableMessages) > $this->maxStableMessages) {
                    array_shift($this->stableMessages);
                }

                continue;
            }

            // Regular log line — strip cursor-reset control sequences.
            $line = preg_replace('/\e\[(?:1)?G\e\[2K/', '', $line);

            // Wrap and add to ring buffer.
            $this->addLogLines($line);
        }
    }

    /**
     * Wrap a log line and append to the ring buffer, trimming to the limit.
     */
    protected function addLogLines(string $line): void
    {
        $width = $this->terminal()->cols() - 10;
        $plainText = $this->stripEscapeSequences($line);

        if (mb_strwidth($plainText) > $width) {
            $wrapped = $this->ansiWordwrap($line, $width);
        } else {
            $wrapped = [$line];
        }

        array_push($this->logs, ...$wrapped);

        while (count($this->logs) > $this->limit) {
            array_shift($this->logs);
        }
    }

    /**
     * Replace the in-progress partial lines with the full accumulated text.
     */
    protected function replacePartialLines(string $text): void
    {
        if ($this->partialStartIndex === null) {
            $this->partialStartIndex = count($this->logs);
        }

        // Truncate back to where the partial started.
        $this->logs = array_slice($this->logs, 0, $this->partialStartIndex);

        // Wrap and append the full accumulated partial text.
        $width = $this->terminal()->cols() - 10;
        $plainText = $this->stripEscapeSequences($text);

        if (mb_strwidth($plainText) > $width) {
            $wrapped = $this->ansiWordwrap($text, $width);
        } else {
            $wrapped = [$text];
        }

        array_push($this->logs, ...$wrapped);

        while (count($this->logs) > $this->limit) {
            array_shift($this->logs);
            $this->partialStartIndex = max(0, $this->partialStartIndex - 1);
        }
    }

    /**
     * Recompute the stable-message budget based on the current sub-label state.
     */
    protected function recalculateMaxStableMessages(): void
    {
        $reserved = 2 + ($this->subLabel !== null && $this->subLabel !== '' ? 1 : 0);
        $this->maxStableMessages = max(0, $this->terminal()->lines() - 10 - $this->limit - $reserved);
    }

    /**
     * Reset the terminal.
     */
    protected function resetTerminal(bool $originalAsync, bool $success = true): void
    {
        $this->finished = true;

        pcntl_async_signals($originalAsync);
        pcntl_signal(SIGINT, SIG_DFL);

        if ($this->socket !== null) {
            fclose($this->socket);
            $this->socket = null;
        }

        if ($this->keepSummary && count($this->stableMessages) > 0) {
            $this->render();

            return;
        }

        $this->eraseRenderedLines();

        if ($this->keepSummary && $success && count($this->stableMessages) === 0) {
            $this->printCompletionLine();
        }
    }

    /**
     * Render a static version of the task.
     *
     * @template TReturn of mixed
     *
     * @param  Closure(Logger): TReturn  $callback
     * @return TReturn
     */
    protected function renderStatically(Closure $callback): mixed
    {
        $this->static = true;

        try {
            $this->hideCursor();
            $this->render();

            $logger = new Logger($this->identifier);
            $result = $callback($logger);
        } catch (\Throwable $e) {
            $this->eraseRenderedLines();

            throw $e;
        }

        $this->eraseRenderedLines();

        if ($this->keepSummary && count($this->stableMessages) === 0) {
            $this->printCompletionLine();
        }

        return $result;
    }

    /**
     * Print a single-line completion indicator after the task has finished.
     */
    protected function printCompletionLine(): void
    {
        static::output()->writeln(' '.$this->green('✔').' '.$this->label);
    }

    /**
     * Disable prompting for input.
     *
     * @throws RuntimeException
     */
    public function prompt(): never
    {
        throw new RuntimeException('Task cannot be prompted.');
    }

    /**
     * Get the current value of the prompt.
     */
    public function value(): bool
    {
        return true;
    }

    /**
     * Clear the lines rendered by the task.
     */
    protected function eraseRenderedLines(): void
    {
        $lines = explode(PHP_EOL, $this->prevFrame);
        $this->moveCursor(-999, -count($lines) + 1);
        $this->eraseDown();
    }

    /**
     * Clean up after the task.
     */
    public function __destruct()
    {
        if (! empty($this->pid)) {
            posix_kill($this->pid, SIGHUP);
        }

        parent::__destruct();
    }
}
