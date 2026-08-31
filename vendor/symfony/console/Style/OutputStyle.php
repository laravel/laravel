<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Style;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Decorates output to add console style guide helpers.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class OutputStyle implements OutputInterface, StyleInterface
{
    public function __construct(
        private OutputInterface $output,
    ) {
    }

    public function newLine(int $count = 1): void
    {
        $this->output->write(str_repeat(\PHP_EOL, $count));
    }

    public function createProgressBar(int $max = 0): ProgressBar
    {
        return new ProgressBar($this->output, $max);
    }

    public function write(string|iterable $messages, bool $newline = false, int $type = self::OUTPUT_NORMAL): void
    {
        $this->output->write($messages, $newline, $type);
    }

    public function writeln(string|iterable $messages, int $type = self::OUTPUT_NORMAL): void
    {
        $this->output->writeln($messages, $type);
    }

    public function setVerbosity(int $level): void
    {
        $this->output->setVerbosity($level);
    }

    public function getVerbosity(): int
    {
        return $this->output->getVerbosity();
    }

    public function setDecorated(bool $decorated): void
    {
        $this->output->setDecorated($decorated);
    }

    public function isDecorated(): bool
    {
        return $this->output->isDecorated();
    }

    public function setFormatter(OutputFormatterInterface $formatter): void
    {
        $this->output->setFormatter($formatter);
    }

    public function getFormatter(): OutputFormatterInterface
    {
        return $this->output->getFormatter();
    }

    public function isSilent(): bool
    {
        // @deprecated since Symfony 7.2, change to $this->output->isSilent() in 8.0
        return method_exists($this->output, 'isSilent') ? $this->output->isSilent() : self::VERBOSITY_SILENT === $this->output->getVerbosity();
    }

    public function isQuiet(): bool
    {
        return $this->output->isQuiet();
    }

    public function isVerbose(): bool
    {
        return $this->output->isVerbose();
    }

    public function isVeryVerbose(): bool
    {
        return $this->output->isVeryVerbose();
    }

    public function isDebug(): bool
    {
        return $this->output->isDebug();
    }

    protected function getErrorOutput(): OutputInterface
    {
        if (!$this->output instanceof ConsoleOutputInterface) {
            return $this->output;
        }

        return $this->output->getErrorOutput();
    }
}
