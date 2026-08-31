<?php

namespace Laravel\Pail;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Laravel\Pail\Printers\CliPrinter;
use Laravel\Pail\ValueObjects\MessageLogged;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessFactory
{
    /**
     * Creates a new instance of the process factory.
     */
    public function run(File $file, OutputInterface $output, string $basePath, Options $options): void
    {
        $printer = new CliPrinter($output, $basePath);

        $remainingBuffer = '';

        Process::timeout($options->timeout())
            ->tty(false)
            ->run(
                $this->command($file),
                function (string $type, string $buffer) use ($options, $printer, &$remainingBuffer, $output) {
                    $lines = Str::of($buffer)->explode("\n");

                    if ($remainingBuffer !== '' && isset($lines[0])) {
                        $lines[0] = $remainingBuffer.$lines[0];
                        $remainingBuffer = '';
                    }

                    if ($lines->last() === '') {
                        $lines = $lines->slice(0, -1);
                    } elseif (! str_ends_with((string) $lines->last(), "\n")) {
                        $remainingBuffer = $lines->pop();
                    }

                    $lines
                        ->filter(fn (string $line) => $line !== '')
                        ->map(function (string $line) use ($output): ?MessageLogged {
                            try {
                                return MessageLogged::fromJson($line);
                            } catch (\Throwable) {
                                $output->writeln('  <fg=yellow>⚠ Pail skipped a malformed log line.</>');

                                return null;
                            }
                        })
                        ->filter()
                        ->filter(fn (MessageLogged $messageLogged) => $options->accepts($messageLogged))
                        ->each(fn (MessageLogged $messageLogged) => $printer->print($messageLogged));
                }
            );
    }

    /**
     * Returns the raw command.
     */
    protected function command(File $file): string
    {
        return '\\tail -F "'.$file->__toString().'"';
    }
}
