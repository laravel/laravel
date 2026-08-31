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

use Symfony\Component\Console\Cursor;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;

/**
 * The ProgressBar provides helpers to display progress output.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Chris Jones <leeked@gmail.com>
 */
final class ProgressBar
{
    public const FORMAT_VERBOSE = 'verbose';
    public const FORMAT_VERY_VERBOSE = 'very_verbose';
    public const FORMAT_DEBUG = 'debug';
    public const FORMAT_NORMAL = 'normal';

    private const FORMAT_VERBOSE_NOMAX = 'verbose_nomax';
    private const FORMAT_VERY_VERBOSE_NOMAX = 'very_verbose_nomax';
    private const FORMAT_DEBUG_NOMAX = 'debug_nomax';
    private const FORMAT_NORMAL_NOMAX = 'normal_nomax';

    private int $barWidth = 28;
    private string $barChar;
    private string $emptyBarChar = '-';
    private string $progressChar = '>';
    private ?string $format = null;
    private ?string $internalFormat = null;
    private ?int $redrawFreq = 1;
    private int $writeCount = 0;
    private float $lastWriteTime = 0;
    private float $minSecondsBetweenRedraws = 0;
    private float $maxSecondsBetweenRedraws = 1;
    private OutputInterface $output;
    private int $step = 0;
    private int $startingStep = 0;
    private ?int $max = null;
    private int $startTime;
    private int $stepWidth;
    private float $percent = 0.0;
    private array $messages = [];
    private bool $overwrite = true;
    private Terminal $terminal;
    private ?string $previousMessage = null;
    private Cursor $cursor;
    private array $placeholders = [];

    private static array $formatters;
    private static array $formats;

    /**
     * @param int $max Maximum steps (0 if unknown)
     */
    public function __construct(OutputInterface $output, int $max = 0, float $minSecondsBetweenRedraws = 1 / 25)
    {
        if ($output instanceof ConsoleOutputInterface) {
            $output = $output->getErrorOutput();
        }

        $this->output = $output;
        $this->setMaxSteps($max);
        $this->terminal = new Terminal();

        if (0 < $minSecondsBetweenRedraws) {
            $this->redrawFreq = null;
            $this->minSecondsBetweenRedraws = $minSecondsBetweenRedraws;
        }

        if (!$this->output->isDecorated()) {
            // disable overwrite when output does not support ANSI codes.
            $this->overwrite = false;

            // set a reasonable redraw frequency so output isn't flooded
            $this->redrawFreq = null;
        }

        $this->startTime = time();
        $this->cursor = new Cursor($output);
    }

    /**
     * Sets a placeholder formatter for a given name, globally for all instances of ProgressBar.
     *
     * This method also allow you to override an existing placeholder.
     *
     * @param string                       $name     The placeholder name (including the delimiter char like %)
     * @param callable(ProgressBar):string $callable A PHP callable
     */
    public static function setPlaceholderFormatterDefinition(string $name, callable $callable): void
    {
        self::$formatters ??= self::initPlaceholderFormatters();

        self::$formatters[$name] = $callable;
    }

    /**
     * Gets the placeholder formatter for a given name.
     *
     * @param string $name The placeholder name (including the delimiter char like %)
     */
    public static function getPlaceholderFormatterDefinition(string $name): ?callable
    {
        self::$formatters ??= self::initPlaceholderFormatters();

        return self::$formatters[$name] ?? null;
    }

    /**
     * Sets a placeholder formatter for a given name, for this instance only.
     *
     * @param callable(ProgressBar):string $callable A PHP callable
     */
    public function setPlaceholderFormatter(string $name, callable $callable): void
    {
        $this->placeholders[$name] = $callable;
    }

    /**
     * Gets the placeholder formatter for a given name.
     *
     * @param string $name The placeholder name (including the delimiter char like %)
     */
    public function getPlaceholderFormatter(string $name): ?callable
    {
        return $this->placeholders[$name] ?? $this::getPlaceholderFormatterDefinition($name);
    }

    /**
     * Sets a format for a given name.
     *
     * This method also allow you to override an existing format.
     *
     * @param string $name   The format name
     * @param string $format A format string
     */
    public static function setFormatDefinition(string $name, string $format): void
    {
        self::$formats ??= self::initFormats();

        self::$formats[$name] = $format;
    }

    /**
     * Gets the format for a given name.
     *
     * @param string $name The format name
     */
    public static function getFormatDefinition(string $name): ?string
    {
        self::$formats ??= self::initFormats();

        return self::$formats[$name] ?? null;
    }

    /**
     * Associates a text with a named placeholder.
     *
     * The text is displayed when the progress bar is rendered but only
     * when the corresponding placeholder is part of the custom format line
     * (by wrapping the name with %).
     *
     * @param string $message The text to associate with the placeholder
     * @param string $name    The name of the placeholder
     */
    public function setMessage(string $message, string $name = 'message'): void
    {
        $this->messages[$name] = $message;
    }

    public function getMessage(string $name = 'message'): ?string
    {
        return $this->messages[$name] ?? null;
    }

    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function getMaxSteps(): int
    {
        return $this->max ?? 0;
    }

    public function getProgress(): int
    {
        return $this->step;
    }

    private function getStepWidth(): int
    {
        return $this->stepWidth;
    }

    public function getProgressPercent(): float
    {
        return $this->percent;
    }

    public function getBarOffset(): float
    {
        return floor(null !== $this->max ? $this->percent * $this->barWidth : (null === $this->redrawFreq ? (int) (min(5, $this->barWidth / 15) * $this->writeCount) : $this->step) % $this->barWidth);
    }

    public function getEstimated(): float
    {
        if (0 === $this->step || $this->step === $this->startingStep) {
            return 0;
        }

        return round((time() - $this->startTime) / ($this->step - $this->startingStep) * $this->max);
    }

    public function getRemaining(): float
    {
        if (0 === $this->step || $this->step === $this->startingStep) {
            return 0;
        }

        return round((time() - $this->startTime) / ($this->step - $this->startingStep) * ($this->max - $this->step));
    }

    public function setBarWidth(int $size): void
    {
        $this->barWidth = max(1, $size);
    }

    public function getBarWidth(): int
    {
        return $this->barWidth;
    }

    public function setBarCharacter(string $char): void
    {
        $this->barChar = $char;
    }

    public function getBarCharacter(): string
    {
        return $this->barChar ?? (null !== $this->max ? '=' : $this->emptyBarChar);
    }

    public function setEmptyBarCharacter(string $char): void
    {
        $this->emptyBarChar = $char;
    }

    public function getEmptyBarCharacter(): string
    {
        return $this->emptyBarChar;
    }

    public function setProgressCharacter(string $char): void
    {
        $this->progressChar = $char;
    }

    public function getProgressCharacter(): string
    {
        return $this->progressChar;
    }

    public function setFormat(string $format): void
    {
        $this->format = null;
        $this->internalFormat = $format;
    }

    /**
     * Sets the redraw frequency.
     *
     * @param int|null $freq The frequency in steps
     */
    public function setRedrawFrequency(?int $freq): void
    {
        $this->redrawFreq = null !== $freq ? max(1, $freq) : null;
    }

    public function minSecondsBetweenRedraws(float $seconds): void
    {
        $this->minSecondsBetweenRedraws = $seconds;
    }

    public function maxSecondsBetweenRedraws(float $seconds): void
    {
        $this->maxSecondsBetweenRedraws = $seconds;
    }

    /**
     * Returns an iterator that will automatically update the progress bar when iterated.
     *
     * @template TKey
     * @template TValue
     *
     * @param iterable<TKey, TValue> $iterable
     * @param int|null               $max      Number of steps to complete the bar (0 if indeterminate), if null it will be inferred from $iterable
     *
     * @return iterable<TKey, TValue>
     */
    public function iterate(iterable $iterable, ?int $max = null): iterable
    {
        if (0 === $max) {
            $max = null;
        }

        $max ??= is_countable($iterable) ? \count($iterable) : null;

        if (0 === $max) {
            $this->max = 0;
            $this->stepWidth = 2;
            $this->finish();

            return;
        }

        $this->start($max);

        foreach ($iterable as $key => $value) {
            yield $key => $value;

            $this->advance();
        }

        $this->finish();
    }

    /**
     * Starts the progress output.
     *
     * @param int|null $max     Number of steps to complete the bar (0 if indeterminate), null to leave unchanged
     * @param int      $startAt The starting point of the bar (useful e.g. when resuming a previously started bar)
     */
    public function start(?int $max = null, int $startAt = 0): void
    {
        $this->startTime = time();
        $this->step = $startAt;
        $this->startingStep = $startAt;

        $startAt > 0 ? $this->setProgress($startAt) : $this->percent = 0.0;

        if (null !== $max) {
            $this->setMaxSteps($max);
        }

        $this->display();
    }

    /**
     * Advances the progress output X steps.
     *
     * @param int $step Number of steps to advance
     */
    public function advance(int $step = 1): void
    {
        $this->setProgress($this->step + $step);
    }

    /**
     * Sets whether to overwrite the progressbar, false for new line.
     */
    public function setOverwrite(bool $overwrite): void
    {
        $this->overwrite = $overwrite;
    }

    public function setProgress(int $step): void
    {
        if ($this->max && $step > $this->max) {
            $this->max = $step;
        } elseif ($step < 0) {
            $step = 0;
        }

        $redrawFreq = $this->redrawFreq ?? (($this->max ?? 10) / 10);
        $prevPeriod = $redrawFreq ? (int) ($this->step / $redrawFreq) : 0;
        $currPeriod = $redrawFreq ? (int) ($step / $redrawFreq) : 0;
        $this->step = $step;
        $this->percent = match ($this->max) {
            null => 0,
            0 => 1,
            default => (float) $this->step / $this->max,
        };
        $timeInterval = microtime(true) - $this->lastWriteTime;

        // Draw regardless of other limits
        if ($this->max === $step) {
            $this->display();

            return;
        }

        // Throttling
        if ($timeInterval < $this->minSecondsBetweenRedraws) {
            return;
        }

        // Draw each step period, but not too late
        if ($prevPeriod !== $currPeriod || $timeInterval >= $this->maxSecondsBetweenRedraws) {
            $this->display();
        }
    }

    public function setMaxSteps(?int $max): void
    {
        if (0 === $max) {
            $max = null;
        }

        $this->format = null;
        if (null === $max) {
            $this->max = null;
            $this->stepWidth = 4;
        } else {
            $this->max = max(0, $max);
            $this->stepWidth = Helper::width((string) $this->max);
        }
    }

    /**
     * Finishes the progress output.
     */
    public function finish(): void
    {
        if (null === $this->max) {
            $this->max = $this->step;
        }

        if (($this->step === $this->max || null === $this->max) && !$this->overwrite) {
            // prevent double 100% output
            return;
        }

        $this->setProgress($this->max ?? $this->step);
    }

    /**
     * Outputs the current progress string.
     */
    public function display(): void
    {
        if (OutputInterface::VERBOSITY_QUIET === $this->output->getVerbosity()) {
            return;
        }

        if (null === $this->format) {
            $this->setRealFormat($this->internalFormat ?: $this->determineBestFormat());
        }

        $this->overwrite($this->buildLine());
    }

    /**
     * Removes the progress bar from the current line.
     *
     * This is useful if you wish to write some output
     * while a progress bar is running.
     * Call display() to show the progress bar again.
     */
    public function clear(): void
    {
        if (!$this->overwrite) {
            return;
        }

        if (null === $this->format) {
            $this->setRealFormat($this->internalFormat ?: $this->determineBestFormat());
        }

        $this->overwrite('');
    }

    private function setRealFormat(string $format): void
    {
        // try to use the _nomax variant if available
        if (!$this->max && null !== self::getFormatDefinition($format.'_nomax')) {
            $this->format = self::getFormatDefinition($format.'_nomax');
        } elseif (null !== self::getFormatDefinition($format)) {
            $this->format = self::getFormatDefinition($format);
        } else {
            $this->format = $format;
        }
    }

    /**
     * Overwrites a previous message to the output.
     */
    private function overwrite(string $message): void
    {
        if ($this->previousMessage === $message) {
            return;
        }

        $originalMessage = $message;

        if ($this->overwrite) {
            if (null !== $this->previousMessage) {
                if ($this->output instanceof ConsoleSectionOutput) {
                    $messageLines = explode("\n", $this->previousMessage);
                    $lineCount = \count($messageLines);

                    $lastLineWithoutDecoration = Helper::removeDecoration($this->output->getFormatter(), end($messageLines) ?? '');

                    // When the last previous line is empty (without formatting) it is already cleared by the section output, so we don't need to clear it again
                    if ('' === $lastLineWithoutDecoration) {
                        --$lineCount;
                    }

                    foreach ($messageLines as $messageLine) {
                        $messageLineLength = Helper::width(Helper::removeDecoration($this->output->getFormatter(), $messageLine));
                        if ($messageLineLength > $this->terminal->getWidth()) {
                            $lineCount += floor($messageLineLength / $this->terminal->getWidth());
                        }
                    }

                    $this->output->clear($lineCount);
                } else {
                    $lineCount = substr_count($this->previousMessage, "\n");
                    for ($i = 0; $i < $lineCount; ++$i) {
                        $this->cursor->moveToColumn(1);
                        $this->cursor->clearLine();
                        $this->cursor->moveUp();
                    }

                    $this->cursor->moveToColumn(1);
                    $this->cursor->clearLine();
                }
            }
        } elseif ($this->step > 0) {
            $message = \PHP_EOL.$message;
        }

        $this->previousMessage = $originalMessage;
        $this->lastWriteTime = microtime(true);

        $this->output->write($message);
        ++$this->writeCount;
    }

    private function determineBestFormat(): string
    {
        return match ($this->output->getVerbosity()) {
            // OutputInterface::VERBOSITY_QUIET: display is disabled anyway
            OutputInterface::VERBOSITY_VERBOSE => $this->max ? self::FORMAT_VERBOSE : self::FORMAT_VERBOSE_NOMAX,
            OutputInterface::VERBOSITY_VERY_VERBOSE => $this->max ? self::FORMAT_VERY_VERBOSE : self::FORMAT_VERY_VERBOSE_NOMAX,
            OutputInterface::VERBOSITY_DEBUG => $this->max ? self::FORMAT_DEBUG : self::FORMAT_DEBUG_NOMAX,
            default => $this->max ? self::FORMAT_NORMAL : self::FORMAT_NORMAL_NOMAX,
        };
    }

    private static function initPlaceholderFormatters(): array
    {
        return [
            'bar' => static function (self $bar, OutputInterface $output) {
                $completeBars = $bar->getBarOffset();
                $display = str_repeat($bar->getBarCharacter(), $completeBars);
                if ($completeBars < $bar->getBarWidth()) {
                    $emptyBars = $bar->getBarWidth() - $completeBars - Helper::length(Helper::removeDecoration($output->getFormatter(), $bar->getProgressCharacter()));
                    $display .= $bar->getProgressCharacter().str_repeat($bar->getEmptyBarCharacter(), $emptyBars);
                }

                return $display;
            },
            'elapsed' => static fn (self $bar) => Helper::formatTime(time() - $bar->getStartTime(), 2),
            'remaining' => static function (self $bar) {
                if (null === $bar->max) {
                    throw new LogicException('Unable to display the remaining time if the maximum number of steps is not set.');
                }

                return Helper::formatTime($bar->getRemaining(), 2);
            },
            'estimated' => static function (self $bar) {
                if (null === $bar->max) {
                    throw new LogicException('Unable to display the estimated time if the maximum number of steps is not set.');
                }

                return Helper::formatTime($bar->getEstimated(), 2);
            },
            'memory' => static fn (self $bar) => Helper::formatMemory(memory_get_usage(true)),
            'current' => static fn (self $bar) => str_pad($bar->getProgress(), $bar->getStepWidth(), ' ', \STR_PAD_LEFT),
            'max' => static fn (self $bar) => $bar->getMaxSteps(),
            'percent' => static fn (self $bar) => floor($bar->getProgressPercent() * 100),
        ];
    }

    private static function initFormats(): array
    {
        return [
            self::FORMAT_NORMAL => ' %current%/%max% [%bar%] %percent:3s%%',
            self::FORMAT_NORMAL_NOMAX => ' %current% [%bar%]',

            self::FORMAT_VERBOSE => ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%',
            self::FORMAT_VERBOSE_NOMAX => ' %current% [%bar%] %elapsed:6s%',

            self::FORMAT_VERY_VERBOSE => ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%',
            self::FORMAT_VERY_VERBOSE_NOMAX => ' %current% [%bar%] %elapsed:6s%',

            self::FORMAT_DEBUG => ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%',
            self::FORMAT_DEBUG_NOMAX => ' %current% [%bar%] %elapsed:6s% %memory:6s%',
        ];
    }

    private function buildLine(): string
    {
        \assert(null !== $this->format);

        $regex = '{%([a-z\-_]+)(?:\:([^%]+))?%}i';
        $callback = function ($matches) {
            if ($formatter = $this->getPlaceholderFormatter($matches[1])) {
                $text = $formatter($this, $this->output);
            } elseif (isset($this->messages[$matches[1]])) {
                $text = $this->messages[$matches[1]];
            } else {
                return $matches[0];
            }

            if (isset($matches[2])) {
                $text = \sprintf('%'.$matches[2], $text);
            }

            return $text;
        };
        $line = preg_replace_callback($regex, $callback, $this->format);

        // gets string length for each sub line with multiline format
        $linesLength = array_map(fn ($subLine) => Helper::width(Helper::removeDecoration($this->output->getFormatter(), rtrim($subLine, "\r"))), explode("\n", $line));

        $linesWidth = max($linesLength);

        $terminalWidth = $this->terminal->getWidth();
        if ($linesWidth <= $terminalWidth) {
            return $line;
        }

        $this->setBarWidth($this->barWidth - $linesWidth + $terminalWidth);

        return preg_replace_callback($regex, $callback, $this->format);
    }
}
