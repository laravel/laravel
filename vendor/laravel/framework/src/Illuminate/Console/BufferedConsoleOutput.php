<?php

namespace Illuminate\Console;

use Symfony\Component\Console\Output\ConsoleOutput;

class BufferedConsoleOutput extends ConsoleOutput
{
    /**
     * The current buffer.
     *
     * @var string
     */
    protected $buffer = '';

    /**
     * Empties the buffer and returns its content.
     *
     * @return string
     */
    public function fetch()
    {
        return tap($this->buffer, function () {
            $this->buffer = '';
        });
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    protected function doWrite(string $message, bool $newline): void
    {
        $this->buffer .= $message;

        if ($newline) {
            $this->buffer .= \PHP_EOL;
        }

        parent::doWrite($message, $newline);
    }
}
