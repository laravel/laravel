<?php

namespace Laravel\Prompts\Support;

class Logger
{
    /**
     * Create a new Logger instance.
     *
     * @param  resource|null  $socket
     */
    public function __construct(protected string $identifier, protected $socket = null)
    {
        //
    }

    /**
     * The buffer for streaming text.
     */
    protected string $streamBuffer = '';

    /**
     * Log a line to the process log.
     */
    public function line(string $message): void
    {
        $this->write(rtrim($message));
    }

    /**
     * Append a chunk of text, accumulating on the current line(s).
     */
    public function partial(string $chunk): void
    {
        $this->streamBuffer .= $chunk;
        $this->write($this->streamBuffer, 'partial');
    }

    /**
     * Commit the accumulated partial text and start fresh.
     */
    public function commitPartial(): void
    {
        $this->streamBuffer = '';
        $this->write('', 'commitpartial');
    }

    /**
     * Log a success message to the process log.
     */
    public function success(string $message): void
    {
        $this->write($message, 'success');
    }

    /**
     * Log a warning message to the process log.
     */
    public function warning(string $message): void
    {
        $this->write($message, 'warning');
    }

    /**
     * Log an error message to the process log.
     */
    public function error(string $message): void
    {
        $this->write($message, 'error');
    }

    /**
     * Update the label of the process log.
     */
    public function label(string $message): void
    {
        $this->write($message, 'label');
    }

    /**
     * Update the sub-label of the process log. Pass an empty string to clear.
     */
    public function subLabel(string $message): void
    {
        $this->write($message, 'sublabel');
    }

    /**
     * Write a message to the socket.
     */
    protected function write(string $message, ?string $type = null): void
    {
        if ($this->socket === null) {
            return;
        }

        if ($type !== null) {
            fwrite($this->socket, $this->prefix($type, $message).PHP_EOL);
        } else {
            fwrite($this->socket, $message.PHP_EOL);
        }
    }

    /**
     * Prefix a message with the identifier and type.
     */
    protected function prefix(string $type, string $message): string
    {
        return $this->identifier.'_'.$type.':'.rtrim($message, PHP_EOL);
    }
}
