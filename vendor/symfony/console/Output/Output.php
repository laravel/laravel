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

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

/**
 * Base class for output classes.
 *
 * There are six levels of verbosity:
 *
 *  * normal: no option passed (normal output)
 *  * verbose: -v (more output)
 *  * very verbose: -vv (highly extended output)
 *  * debug: -vvv (all debug output)
 *  * quiet: -q (only output errors)
 *  * silent: --silent (no output)
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class Output implements OutputInterface
{
    private int $verbosity;
    private OutputFormatterInterface $formatter;

    /**
     * @param int|null                      $verbosity The verbosity level (one of the VERBOSITY constants in OutputInterface)
     * @param bool                          $decorated Whether to decorate messages
     * @param OutputFormatterInterface|null $formatter Output formatter instance (null to use default OutputFormatter)
     */
    public function __construct(?int $verbosity = self::VERBOSITY_NORMAL, bool $decorated = false, ?OutputFormatterInterface $formatter = null)
    {
        $this->verbosity = $verbosity ?? self::VERBOSITY_NORMAL;
        $this->formatter = $formatter ?? new OutputFormatter();
        $this->formatter->setDecorated($decorated);
    }

    public function setFormatter(OutputFormatterInterface $formatter): void
    {
        $this->formatter = $formatter;
    }

    public function getFormatter(): OutputFormatterInterface
    {
        return $this->formatter;
    }

    public function setDecorated(bool $decorated): void
    {
        $this->formatter->setDecorated($decorated);
    }

    public function isDecorated(): bool
    {
        return $this->formatter->isDecorated();
    }

    public function setVerbosity(int $level): void
    {
        $this->verbosity = $level;
    }

    public function getVerbosity(): int
    {
        return $this->verbosity;
    }

    public function isSilent(): bool
    {
        return self::VERBOSITY_SILENT === $this->verbosity;
    }

    public function isQuiet(): bool
    {
        return self::VERBOSITY_QUIET === $this->verbosity;
    }

    public function isVerbose(): bool
    {
        return self::VERBOSITY_VERBOSE <= $this->verbosity;
    }

    public function isVeryVerbose(): bool
    {
        return self::VERBOSITY_VERY_VERBOSE <= $this->verbosity;
    }

    public function isDebug(): bool
    {
        return self::VERBOSITY_DEBUG <= $this->verbosity;
    }

    public function writeln(string|iterable $messages, int $options = self::OUTPUT_NORMAL): void
    {
        $this->write($messages, true, $options);
    }

    public function write(string|iterable $messages, bool $newline = false, int $options = self::OUTPUT_NORMAL): void
    {
        if (!is_iterable($messages)) {
            $messages = [$messages];
        }

        $types = self::OUTPUT_NORMAL | self::OUTPUT_RAW | self::OUTPUT_PLAIN;
        $type = $types & $options ?: self::OUTPUT_NORMAL;

        $verbosities = self::VERBOSITY_QUIET | self::VERBOSITY_NORMAL | self::VERBOSITY_VERBOSE | self::VERBOSITY_VERY_VERBOSE | self::VERBOSITY_DEBUG;
        $verbosity = $verbosities & $options ?: self::VERBOSITY_NORMAL;

        if ($verbosity > $this->getVerbosity()) {
            return;
        }

        foreach ($messages as $message) {
            switch ($type) {
                case OutputInterface::OUTPUT_NORMAL:
                    $message = $this->formatter->format($message);
                    break;
                case OutputInterface::OUTPUT_RAW:
                    break;
                case OutputInterface::OUTPUT_PLAIN:
                    $message = strip_tags($this->formatter->format($message));
                    break;
            }

            $this->doWrite($message ?? '', $newline);
        }
    }

    /**
     * Writes a message to the output.
     */
    abstract protected function doWrite(string $message, bool $newline): void;
}
