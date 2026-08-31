<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console;

use Symfony\Component\Console\Output\AnsiColorMode;

class Terminal
{
    public const DEFAULT_COLOR_MODE = AnsiColorMode::Ansi4;

    private static ?AnsiColorMode $colorMode = null;
    private static ?int $width = null;
    private static ?int $height = null;
    private static ?bool $stty = null;

    /**
     * About Ansi color types: https://en.wikipedia.org/wiki/ANSI_escape_code#Colors
     * For more information about true color support with terminals https://github.com/termstandard/colors/.
     */
    public static function getColorMode(): AnsiColorMode
    {
        // Use Cache from previous run (or user forced mode)
        if (null !== self::$colorMode) {
            return self::$colorMode;
        }

        // Try with $COLORTERM first
        if (\is_string($colorterm = getenv('COLORTERM'))) {
            $colorterm = strtolower($colorterm);

            if (str_contains($colorterm, 'truecolor')) {
                self::setColorMode(AnsiColorMode::Ansi24);

                return self::$colorMode;
            }

            if (str_contains($colorterm, '256color')) {
                self::setColorMode(AnsiColorMode::Ansi8);

                return self::$colorMode;
            }
        }

        // Try with $TERM
        if (\is_string($term = getenv('TERM'))) {
            $term = strtolower($term);

            if (str_contains($term, 'truecolor')) {
                self::setColorMode(AnsiColorMode::Ansi24);

                return self::$colorMode;
            }

            if (str_contains($term, '256color')) {
                self::setColorMode(AnsiColorMode::Ansi8);

                return self::$colorMode;
            }
        }

        self::setColorMode(self::DEFAULT_COLOR_MODE);

        return self::$colorMode;
    }

    /**
     * Force a terminal color mode rendering.
     */
    public static function setColorMode(?AnsiColorMode $colorMode): void
    {
        self::$colorMode = $colorMode;
    }

    /**
     * Gets the terminal width.
     */
    public function getWidth(): int
    {
        $width = getenv('COLUMNS');
        if (false !== $width) {
            return (int) trim($width);
        }

        if (null === self::$width) {
            self::initDimensions();
        }

        return self::$width ?: 80;
    }

    /**
     * Gets the terminal height.
     */
    public function getHeight(): int
    {
        $height = getenv('LINES');
        if (false !== $height) {
            return (int) trim($height);
        }

        if (null === self::$height) {
            self::initDimensions();
        }

        return self::$height ?: 50;
    }

    /**
     * @internal
     */
    public static function hasSttyAvailable(): bool
    {
        if (null !== self::$stty) {
            return self::$stty;
        }

        // skip check if shell_exec function is disabled
        if (!\function_exists('shell_exec')) {
            return false;
        }

        return self::$stty = (bool) @shell_exec('stty 2> '.('\\' === \DIRECTORY_SEPARATOR ? 'NUL' : '/dev/null'));
    }

    private static function initDimensions(): void
    {
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $ansicon = getenv('ANSICON');
            if (false !== $ansicon && preg_match('/^(\d+)x(\d+)(?: \((\d+)x(\d+)\))?$/', trim($ansicon), $matches)) {
                // extract [w, H] from "wxh (WxH)"
                // or [w, h] from "wxh"
                self::$width = (int) $matches[1];
                self::$height = isset($matches[4]) ? (int) $matches[4] : (int) $matches[2];
            } elseif (!sapi_windows_vt100_support(fopen('php://stdout', 'w')) && self::hasSttyAvailable()) {
                // only use stty on Windows if the terminal does not support vt100 (e.g. Windows 7 + git-bash)
                // testing for stty in a Windows 10 vt100-enabled console will implicitly disable vt100 support on STDOUT
                self::initDimensionsUsingStty();
            } elseif (null !== $dimensions = self::getConsoleMode()) {
                // extract [w, h] from "wxh"
                self::$width = (int) $dimensions[0];
                self::$height = (int) $dimensions[1];
            }
        } else {
            self::initDimensionsUsingStty();
        }
    }

    /**
     * Initializes dimensions using the output of an stty columns line.
     */
    private static function initDimensionsUsingStty(): void
    {
        if ($sttyString = self::getSttyColumns()) {
            if (preg_match('/rows.(\d+);.columns.(\d+);/is', $sttyString, $matches)) {
                // extract [w, h] from "rows h; columns w;"
                self::$width = (int) $matches[2];
                self::$height = (int) $matches[1];
            } elseif (preg_match('/;.(\d+).rows;.(\d+).columns/is', $sttyString, $matches)) {
                // extract [w, h] from "; h rows; w columns"
                self::$width = (int) $matches[2];
                self::$height = (int) $matches[1];
            }
        }
    }

    /**
     * Runs and parses mode CON if it's available, suppressing any error output.
     *
     * @return int[]|null An array composed of the width and the height or null if it could not be parsed
     */
    private static function getConsoleMode(): ?array
    {
        $info = self::readFromProcess('mode CON');

        if (null === $info || !preg_match('/--------+\r?\n.+?(\d+)\r?\n.+?(\d+)\r?\n/', $info, $matches)) {
            return null;
        }

        return [(int) $matches[2], (int) $matches[1]];
    }

    /**
     * Runs and parses stty -a if it's available, suppressing any error output.
     */
    private static function getSttyColumns(): ?string
    {
        return self::readFromProcess(['stty', '-a']);
    }

    private static function readFromProcess(string|array $command): ?string
    {
        if (!\function_exists('proc_open')) {
            return null;
        }

        $descriptorspec = [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $cp = \function_exists('sapi_windows_cp_set') ? sapi_windows_cp_get() : 0;

        if (!$process = @proc_open($command, $descriptorspec, $pipes, null, null, ['suppress_errors' => true])) {
            return null;
        }

        $info = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        if ($cp) {
            sapi_windows_cp_set($cp);
        }

        return $info;
    }
}
