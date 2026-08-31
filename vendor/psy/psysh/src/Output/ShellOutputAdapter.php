<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Output;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Adapter for output methods provided by ShellOutput but used by commands.
 *
 * This allows commands to use paging and line numbering with any
 * OutputInterface implementation.
 *
 * @todo On the next major release, consider removing ShellOutput and folding
 *       its functionality entirely into this adapter.
 */
class ShellOutputAdapter implements OutputInterface
{
    // @todo Remove ShellOutput::NUMBER_LINES alias on next major release.
    public const NUMBER_LINES = ShellOutput::NUMBER_LINES;

    private OutputInterface $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Page multiple lines of output.
     *
     * @param string|array|\Closure $messages
     */
    public function page($messages, int $type = 0): void
    {
        if ($this->output instanceof ShellOutput) {
            $this->output->page($messages, $type);

            return;
        }

        if (\is_string($messages)) {
            // Split on newlines to avoid O(n^2) performance in Symfony's OutputFormatter
            // when processing large strings with many style tags.
            $messages = \explode("\n", $messages);
        }

        if (!\is_array($messages) && !\is_callable($messages)) {
            throw new \InvalidArgumentException('Paged output requires a string, array or callback');
        }

        $this->startPaging();

        if (\is_callable($messages)) {
            $messages($this);
        } else {
            $this->write($messages, true, $type);
        }

        $this->stopPaging();
    }

    /**
     * Start sending output to the output pager.
     */
    public function startPaging(): void
    {
        if ($this->output instanceof ShellOutput) {
            $this->output->startPaging();
        }
    }

    /**
     * Stop paging output and flush the output pager.
     */
    public function stopPaging(): void
    {
        if ($this->output instanceof ShellOutput) {
            $this->output->stopPaging();
        }
    }

    /**
     * Writes a message to the output.
     *
     * @param string|array $messages
     */
    public function write($messages, $newline = false, $type = 0): void
    {
        if ($this->output instanceof ShellOutput) {
            $this->output->write($messages, $newline, $type);

            return;
        }

        $messages = (array) $messages;

        if ($type & self::NUMBER_LINES) {
            $messages = $this->numberLines($messages, $type);

            // clean this up for the wrapped output.
            $type = $type & ~self::NUMBER_LINES & ~OutputInterface::OUTPUT_RAW;
        }

        $this->output->write($messages, $newline, $type);
    }

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|array $messages
     */
    public function writeln($messages, $type = 0): void
    {
        $this->write($messages, true, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function setVerbosity($level): void
    {
        $this->output->setVerbosity($level);
    }

    /**
     * {@inheritdoc}
     */
    public function getVerbosity(): int
    {
        return $this->output->getVerbosity();
    }

    /**
     * {@inheritdoc}
     */
    public function isQuiet(): bool
    {
        return $this->output->isQuiet();
    }

    /**
     * {@inheritdoc}
     */
    public function isVerbose(): bool
    {
        return $this->output->isVerbose();
    }

    /**
     * {@inheritdoc}
     */
    public function isVeryVerbose(): bool
    {
        return $this->output->isVeryVerbose();
    }

    /**
     * @todo Remove method_exists guard when dropping support for Symfony < 7.2.
     *
     * @suppress PhanUndeclaredMethod
     */
    public function isSilent(): bool
    {
        if (\method_exists($this->output, 'isSilent')) {
            return $this->output->isSilent();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug(): bool
    {
        return $this->output->isDebug();
    }

    /**
     * {@inheritdoc}
     */
    public function setDecorated($decorated): void
    {
        $this->output->setDecorated($decorated);
    }

    /**
     * {@inheritdoc}
     */
    public function isDecorated(): bool
    {
        return $this->output->isDecorated();
    }

    /**
     * {@inheritdoc}
     */
    public function setFormatter(OutputFormatterInterface $formatter): void
    {
        $this->output->setFormatter($formatter);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatter(): OutputFormatterInterface
    {
        return $this->output->getFormatter();
    }

    /**
     * Add line numbers to a message array.
     *
     * @param string[] $messages
     *
     * @return string[]
     */
    private function numberLines(array $messages, int $type): array
    {
        $pad = \strlen((string) \count($messages));
        $template = $this->output->isDecorated() && $this->output->getFormatter()->hasStyle('whisper')
            ? "<whisper>%{$pad}s:</whisper> %s"
            : "%{$pad}s: %s";

        if ($type & OutputInterface::OUTPUT_RAW) {
            $messages = \array_map([OutputFormatter::class, 'escape'], $messages);
        }

        foreach ($messages as $i => $line) {
            $messages[$i] = \sprintf($template, $i, $line);
        }

        return $messages;
    }
}
