<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Output;

use Symfony\Component\Console\Formatter\NullOutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

/**
 * NullOutput suppresses all output.
 *
 *     $output = new NullOutput();
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 */
class NullOutput implements OutputInterface
{
    private NullOutputFormatter $formatter;

    public function setFormatter(OutputFormatterInterface $formatter): void
    {
        // do nothing
    }

    public function getFormatter(): OutputFormatterInterface
    {
        // to comply with the interface we must return a OutputFormatterInterface
        return $this->formatter ??= new NullOutputFormatter();
    }

    public function setDecorated(bool $decorated): void
    {
        // do nothing
    }

    public function isDecorated(): bool
    {
        return false;
    }

    public function setVerbosity(int $level): void
    {
        // do nothing
    }

    public function getVerbosity(): int
    {
        return self::VERBOSITY_SILENT;
    }

    public function isSilent(): bool
    {
        return true;
    }

    public function isQuiet(): bool
    {
        return false;
    }

    public function isVerbose(): bool
    {
        return false;
    }

    public function isVeryVerbose(): bool
    {
        return false;
    }

    public function isDebug(): bool
    {
        return false;
    }

    public function writeln(string|iterable $messages, int $options = self::OUTPUT_NORMAL): void
    {
        // do nothing
    }

    public function write(string|iterable $messages, bool $newline = false, int $options = self::OUTPUT_NORMAL): void
    {
        // do nothing
    }
}
