<?php

namespace Laravel\Pail\Printers;

use Illuminate\Support\Collection;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use Laravel\Pail\Contracts\Printer;
use Laravel\Pail\ValueObjects\MessageLogged;
use Laravel\Pail\ValueObjects\Origin\Http;
use Laravel\Pail\ValueObjects\Origin\Queue;
use Symfony\Component\Console\Output\OutputInterface;

use function Termwind\render;
use function Termwind\renderUsing;
use function Termwind\terminal;

class CliPrinter implements Printer
{
    /**
     * {@inheritDoc}
     */
    public function print(MessageLogged $messageLogged): void
    {
        $classOrType = $this->truncateClassOrType($messageLogged->classOrType());
        $color = $messageLogged->color();
        $message = $this->truncateMessage($messageLogged->message());
        $date = $this->output->isVerbose() ? $messageLogged->date() : $messageLogged->time();

        $fileHtml = $this->fileHtml($messageLogged->file(), $classOrType);
        $messageHtml = $this->messageHtml($message);
        $optionsHtml = $this->optionsHtml($messageLogged);
        $traceHtml = $this->traceHtml($messageLogged);

        $messageClasses = $this->output->isVerbose() ? '' : 'truncate';

        $endingTopRight = $this->output->isVerbose() ? '' : '┐';
        $endingMiddle = $this->output->isVerbose() ? '' : '│';
        $endingBottomRight = $this->output->isVerbose() ? '' : '┘';

        renderUsing($this->output);
        render(<<<HTML
            <div class="max-w-150">
                <div class="flex">
                    <div>
                        <span class="mr-1 text-gray">┌</span>
                        <span class="text-gray">$date</span>
                        <span class="px-1 text-$color font-bold">$classOrType</span>
                    </div>
                    <span class="flex-1 content-repeat-[─] text-gray"></span>
                    <span class="text-gray">
                        $fileHtml
                        <span class="text-gray">$endingTopRight</span>
                    </span>
                </div>
                <div class="flex $messageClasses">
                    <span>
                        <span class="mr-1 text-gray">│</span>
                        $messageHtml
                    </span>
                    <span class="flex-1"></span>
                    <span class="flex-1 text-gray text-right">$endingMiddle</span>
                </div>
                $traceHtml
                <div class="flex text-gray">
                    <span>└</span>
                    <span class="mr-1 flex-1 content-repeat-[─]"></span>
                    $optionsHtml
                    <span class="ml-1">$endingBottomRight</span>
                </div>
            </div>
        HTML);
    }

    /**
     * Creates a new instance printer instance.
     */
    public function __construct(protected OutputInterface $output, protected string $basePath)
    {
        //
    }

    /**
     * Gets the file html.
     */
    protected function fileHtml(?string $file, string $classOrType): ?string
    {
        if (is_null($file)) {
            return null;
        }

        if (Env::get('PAIL_TESTS') ?? false) {
            $file = $this->basePath.'/app/MyClass.php:12';
        }

        $file = str_replace($this->basePath.'/', '', $file);

        if (! $this->output->isVerbose()) {
            $file = Str::of($file)
                ->explode('/')
                ->when(
                    fn (Collection $file) => $file->count() > 4,
                    fn (Collection $file) => $file->take(2)->merge(
                        ['…', (string) $file->last()],
                    ),
                )->implode('/');

            $fileSize = max(0, min(terminal()->width() - strlen($classOrType) - 16, 145));

            if (strlen($file) > $fileSize) {
                $file = mb_substr($file, 0, $fileSize).'…';
            }
        }

        if ($file === '…') {
            return null;
        }

        $file = str_replace('……', '…', $file);

        return <<<HTML
            <span class="text-gray mx-1">
                $file
            </span>
        HTML;
    }

    /**
     * Gets the message html.
     */
    protected function messageHtml(string $message): string
    {
        if (empty($message)) {
            return '<span class="text-gray">No message.</span>';
        }

        $message = htmlspecialchars($message);

        if (strstr($message, PHP_EOL)) {
            return "<pre>$message</pre>";
        }

        return "<span>$message</span>";
    }

    /**
     * Truncates the class or type, if needed.
     */
    protected function truncateClassOrType(string $classOrType): string
    {
        if ($this->output->isVerbose()) {
            return $classOrType;
        }

        return Str::of($classOrType)
            ->explode('\\')
            ->when(
                fn (Collection $classOrType) => $classOrType->count() > 4,
                fn (Collection $classOrType) => $classOrType->take(2)->merge(
                    ['…', (string) $classOrType->last()]
                ),
            )->implode('\\');
    }

    /**
     * Truncates the message, if needed.
     */
    protected function truncateMessage(string $message): string
    {
        if (! $this->output->isVerbose()) {
            $messageSize = max(0, min(terminal()->width() - 5, 145));

            if (strlen($message) > $messageSize) {
                $message = mb_substr($message, 0, $messageSize).'…';
            }
        }

        return $message;
    }

    /**
     * Gets the options html.
     */
    public function optionsHtml(MessageLogged $messageLogged): string
    {
        $origin = $messageLogged->origin();

        if ($origin instanceof Http) {
            if (str_starts_with($path = $origin->path, '/') === false) {
                $path = '/'.$origin->path;
            }

            $options = [
                strtoupper($origin->method) => $path,
                'Auth ID' => $origin->authId
                    ? ($origin->authId.($origin->authEmail ? " ({$origin->authEmail})" : ''))
                    : 'guest',
            ];
        } elseif ($origin instanceof Queue) {
            $options = [
                $origin->command ? "artisan {$origin->command}" : null,
                $origin->queue,
                $origin->job,
            ];
        } else {
            $options = [
                $origin->command ? "artisan {$origin->command}" : 'artisan',
            ];
        }

        return collect($options)->merge(
            $messageLogged->context()
        )->reject(fn (mixed $value, string|int $key) => is_int($key) && is_null($value))
            ->map(fn (mixed $value) => is_string($value) ? $value : var_export($value, true))
            ->map(fn (string $value) => htmlspecialchars($value))
            ->map(fn (string $value, string|int $key) => is_string($key) ? "$key: $value" : $value)
            ->map(fn (string $value) => "<span class=\"font-bold\">$value</span>")
            ->implode(' • ');
    }

    /**
     * Gets the trace html.
     */
    public function traceHtml(MessageLogged $messageLogged): string
    {
        if (! $this->output->isVeryVerbose()) {
            return '';
        }

        $trace = $messageLogged->trace();

        if (Env::get('PAIL_TESTS') ?? false) {
            $trace = [
                [
                    'line' => 12,
                    'file' => $this->basePath.'/app/MyClass.php',
                ],
                [
                    'line' => 34,
                    'file' => $this->basePath.'/app/MyClass.php',
                ],
            ];
        }

        if (is_null($trace)) {
            return '';
        }

        return collect($trace)
            ->map(function (array $frame, int $index) {
                $number = $index + 1;

                [
                    'line' => $line,
                    'file' => $file,
                ] = $frame;

                $file = str_replace($this->basePath.'/', '', $file);

                $remainingTraces = '';

                if (! $this->output->isVerbose()) {
                    $file = (string) Str::of($file)
                        ->explode('/')
                        ->when(
                            fn (Collection $file) => $file->count() > 4,
                            fn (Collection $file) => $file->take(2)->merge(
                                ['…', (string) $file->last()],
                            ),
                        )->implode('/');
                }

                return <<<HTML
                    <div class="flex text-gray">
                        <span>
                            <span class="mr-1 text-gray">│</span>
                            <span>$number. $file:$line $remainingTraces</span>
                        </span>
                    </div>
                HTML;
            })->implode('');
    }
}
