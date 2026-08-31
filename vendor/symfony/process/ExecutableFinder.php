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
 * Generic executable finder.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ExecutableFinder
{
    private const CMD_BUILTINS = [
        'assoc', 'break', 'call', 'cd', 'chdir', 'cls', 'color', 'copy', 'date',
        'del', 'dir', 'echo', 'endlocal', 'erase', 'exit', 'for', 'ftype', 'goto',
        'help', 'if', 'label', 'md', 'mkdir', 'mklink', 'move', 'path', 'pause',
        'popd', 'prompt', 'pushd', 'rd', 'rem', 'ren', 'rename', 'rmdir', 'set',
        'setlocal', 'shift', 'start', 'time', 'title', 'type', 'ver', 'vol',
    ];

    private array $suffixes = [];

    /**
     * Replaces default suffixes of executable.
     */
    public function setSuffixes(array $suffixes): void
    {
        $this->suffixes = $suffixes;
    }

    /**
     * Adds new possible suffix to check for executable, including the dot (.).
     *
     *     $finder = new ExecutableFinder();
     *     $finder->addSuffix('.foo');
     */
    public function addSuffix(string $suffix): void
    {
        $this->suffixes[] = $suffix;
    }

    /**
     * Finds an executable by name.
     *
     * @param string      $name      The executable name (without the extension)
     * @param string|null $default   The default to return if no executable is found
     * @param array       $extraDirs Additional dirs to check into
     */
    public function find(string $name, ?string $default = null, array $extraDirs = []): ?string
    {
        // windows built-in commands that are present in cmd.exe should not be resolved using PATH as they do not exist as exes
        if ('\\' === \DIRECTORY_SEPARATOR && \in_array(strtolower($name), self::CMD_BUILTINS, true)) {
            return $name;
        }

        $dirs = array_merge(
            explode(\PATH_SEPARATOR, getenv('PATH') ?: getenv('Path') ?: ''),
            $extraDirs
        );

        $suffixes = $this->suffixes;
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $pathExt = getenv('PATHEXT') ?: '';
            $suffixes = array_merge($suffixes, $pathExt ? explode(\PATH_SEPARATOR, $pathExt) : ['.exe', '.bat', '.cmd', '.com']);
        }
        $suffixes = '' !== pathinfo($name, \PATHINFO_EXTENSION) ? array_merge([''], $suffixes) : array_merge($suffixes, ['']);
        foreach ($suffixes as $suffix) {
            foreach ($dirs as $dir) {
                if ('' === $dir) {
                    $dir = '.';
                }
                if (@is_file($file = $dir.\DIRECTORY_SEPARATOR.$name.$suffix) && ('\\' === \DIRECTORY_SEPARATOR || @is_executable($file))) {
                    return $file;
                }

                if (!@is_dir($dir) && basename($dir) === $name.$suffix && @is_executable($dir)) {
                    return $dir;
                }
            }
        }

        if ('\\' === \DIRECTORY_SEPARATOR || !\function_exists('exec') || \strlen($name) !== strcspn($name, '/'.\DIRECTORY_SEPARATOR)) {
            return $default;
        }

        $execResult = exec('command -v -- '.escapeshellarg($name));

        if (($executablePath = substr($execResult, 0, strpos($execResult, \PHP_EOL) ?: null)) && @is_executable($executablePath)) {
            return $executablePath;
        }

        return $default;
    }
}
