<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Process;

/**
 * An executable finder specifically designed for the PHP executable.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class PhpExecutableFinder
{
    private ExecutableFinder $executableFinder;

    public function __construct()
    {
        $this->executableFinder = new ExecutableFinder();
    }

    /**
     * Finds The PHP executable.
     */
    public function find(bool $includeArgs = true): string|false
    {
        if ($php = getenv('PHP_BINARY')) {
            if (!is_executable($php) && !$php = $this->executableFinder->find($php)) {
                return false;
            }

            if (@is_dir($php)) {
                return false;
            }

            return $php;
        }

        $args = $this->findArguments();
        $args = $includeArgs && $args ? ' '.implode(' ', $args) : '';

        // PHP_BINARY return the current sapi executable
        if (\PHP_BINARY && \in_array(\PHP_SAPI, ['cli', 'cli-server', 'phpdbg'], true)) {
            return \PHP_BINARY.$args;
        }

        if ($php = getenv('PHP_PATH')) {
            if (!@is_executable($php) || @is_dir($php)) {
                return false;
            }

            return $php;
        }

        if ($php = getenv('PHP_PEAR_PHP_BIN')) {
            if (@is_executable($php) && !@is_dir($php)) {
                return $php;
            }
        }

        if (@is_executable($php = \PHP_BINDIR.('\\' === \DIRECTORY_SEPARATOR ? '\\php.exe' : '/php')) && !@is_dir($php)) {
            return $php;
        }

        $dirs = [\PHP_BINDIR];
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $dirs[] = 'C:\xampp\php\\';
        }

        if ($herdPath = getenv('HERD_HOME')) {
            $dirs[] = $herdPath.\DIRECTORY_SEPARATOR.'bin';
        }

        return $this->executableFinder->find('php', false, $dirs);
    }

    /**
     * Finds the PHP executable arguments.
     *
     * @return list<non-empty-string>
     */
    public function findArguments(): array
    {
        $arguments = [];
        if ('phpdbg' === \PHP_SAPI) {
            $arguments[] = '-qrr';
        }

        return $arguments;
    }
}
