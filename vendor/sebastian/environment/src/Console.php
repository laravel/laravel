<?php declare(strict_types=1);
/*
 * This file is part of sebastian/environment.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Environment;

use const DIRECTORY_SEPARATOR;
use const STDIN;
use const STDOUT;
use function assert;
use function defined;
use function fclose;
use function fstat;
use function function_exists;
use function getenv;
use function in_array;
use function is_array;
use function is_resource;
use function is_string;
use function posix_isatty;
use function preg_match;
use function proc_close;
use function proc_open;
use function sapi_windows_vt100_support;
use function shell_exec;
use function stream_get_contents;
use function stream_isatty;
use function strtoupper;
use function trim;

final class Console
{
    /**
     * @var int
     */
    public const STDIN = 0;

    /**
     * @var int
     */
    public const STDOUT = 1;

    /**
     * @var int
     */
    public const STDERR = 2;

    /**
     * Returns true if STDOUT supports colorization.
     *
     * This code has been copied and adapted from
     * Symfony\Component\Console\Output\StreamOutput.
     */
    public function hasColorSupport(): bool
    {
        if (!defined('STDOUT')) {
            return false;
        }

        if (isset($_SERVER['NO_COLOR']) || false !== getenv('NO_COLOR')) {
            return false;
        }

        if (!@stream_isatty(STDOUT) &&
            !in_array(strtoupper((string) getenv('MSYSTEM')), ['MINGW32', 'MINGW64'], true)) {
            return false;
        }

        if ($this->isWindows() &&
            function_exists('sapi_windows_vt100_support') &&
            @sapi_windows_vt100_support(STDOUT)) {
            return true;
        }

        if ('Hyper' === getenv('TERM_PROGRAM') ||
            false !== getenv('COLORTERM') ||
            false !== getenv('ANSICON') ||
            'ON' === getenv('ConEmuANSI')) {
            return true;
        }

        if ('dumb' === $term = (string) getenv('TERM')) {
            return false;
        }

        return (bool) preg_match('/^((screen|xterm|vt100|vt220|putty|rxvt|ansi|cygwin|linux).*)|(.*-256(color)?(-bce)?)$/', $term);
    }

    /**
     * Returns the number of columns of the terminal.
     *
     * @codeCoverageIgnore
     */
    public function getNumberOfColumns(): int
    {
        if (!$this->isInteractive(defined('STDIN') ? STDIN : self::STDIN)) {
            return 80;
        }

        if ($this->isWindows()) {
            return $this->getNumberOfColumnsWindows();
        }

        return $this->getNumberOfColumnsInteractive();
    }

    /**
     * Returns if the file descriptor is an interactive terminal or not.
     *
     * Normally, we want to use a resource as a parameter, yet sadly it's not always available,
     * eg when running code in interactive console (`php -a`), STDIN/STDOUT/STDERR constants are not defined.
     *
     * @param int|resource $fileDescriptor
     */
    public function isInteractive($fileDescriptor = self::STDOUT): bool
    {
        if (is_resource($fileDescriptor)) {
            if (function_exists('stream_isatty') && @stream_isatty($fileDescriptor)) {
                return true;
            }

            if (function_exists('fstat')) {
                $stat = @fstat(STDOUT);

                return $stat && 0o020000 === ($stat['mode'] & 0o170000);
            }

            return false;
        }

        return function_exists('posix_isatty') && @posix_isatty($fileDescriptor);
    }

    private function isWindows(): bool
    {
        return DIRECTORY_SEPARATOR === '\\';
    }

    /**
     * @codeCoverageIgnore
     */
    private function getNumberOfColumnsInteractive(): int
    {
        if (function_exists('shell_exec') && preg_match('#\d+ (\d+)#', shell_exec('stty size') ?: '', $match) === 1) {
            if ((int) $match[1] > 0) {
                return (int) $match[1];
            }
        }

        if (function_exists('shell_exec') && preg_match('#columns = (\d+);#', shell_exec('stty') ?: '', $match) === 1) {
            if ((int) $match[1] > 0) {
                return (int) $match[1];
            }
        }

        return 80;
    }

    /**
     * @codeCoverageIgnore
     */
    private function getNumberOfColumnsWindows(): int
    {
        $ansicon = getenv('ANSICON');
        $columns = 80;

        if (is_string($ansicon) && preg_match('/^(\d+)x\d+ \(\d+x(\d+)\)$/', trim($ansicon), $matches)) {
            $columns = (int) $matches[1];
        } elseif (function_exists('proc_open')) {
            $process = proc_open(
                'mode CON',
                [
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w'],
                ],
                $pipes,
                null,
                null,
                ['suppress_errors' => true],
            );

            assert(is_array($pipes));
            assert(isset($pipes[1]) && is_resource($pipes[1]));
            assert(isset($pipes[2]) && is_resource($pipes[2]));

            if (is_resource($process)) {
                $info = stream_get_contents($pipes[1]);

                fclose($pipes[1]);
                fclose($pipes[2]);
                proc_close($process);

                if (preg_match('/--------+\r?\n.+?(\d+)\r?\n.+?(\d+)\r?\n/', (string) $info, $matches)) {
                    $columns = (int) $matches[2];
                }
            }
        }

        return $columns - 1;
    }
}
