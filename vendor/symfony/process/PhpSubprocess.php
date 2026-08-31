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

use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * PhpSubprocess runs a PHP command as a subprocess while keeping the original php.ini settings.
 *
 * For this, it generates a temporary php.ini file taking over all the current settings and disables
 * loading additional .ini files. Basically, your command gets prefixed using "php -n -c /tmp/temp.ini".
 *
 * Given your php.ini contains "memory_limit=-1" and you have a "MemoryTest.php" with the following content:
 *
 *     <?php var_dump(ini_get('memory_limit'));
 *
 * These are the differences between the regular Process and PhpSubprocess classes:
 *
 *     $p = new Process(['php', '-d', 'memory_limit=256M', 'MemoryTest.php']);
 *     $p->run();
 *     print $p->getOutput()."\n";
 *
 * This will output "string(2) "-1", because the process is started with the default php.ini settings.
 *
 *     $p = new PhpSubprocess(['MemoryTest.php'], null, null, 60, ['php', '-d', 'memory_limit=256M']);
 *     $p->run();
 *     print $p->getOutput()."\n";
 *
 * This will output "string(4) "256M"", because the process is started with the temporarily created php.ini settings.
 *
 * @author Yanick Witschi <yanick.witschi@terminal42.ch>
 * @author Partially copied and heavily inspired from composer/xdebug-handler by John Stevenson <john-stevenson@blueyonder.co.uk>
 */
class PhpSubprocess extends Process
{
    /**
     * @param array       $command The command to run and its arguments listed as separate entries. They will automatically
     *                             get prefixed with the PHP binary
     * @param string|null $cwd     The working directory or null to use the working dir of the current PHP process
     * @param array|null  $env     The environment variables or null to use the same environment as the current PHP process
     * @param int         $timeout The timeout in seconds
     * @param array|null  $php     Path to the PHP binary to use with any additional arguments
     */
    public function __construct(array $command, ?string $cwd = null, ?array $env = null, int $timeout = 60, ?array $php = null)
    {
        if (null === $php) {
            $executableFinder = new PhpExecutableFinder();
            $php = $executableFinder->find(false);
            $php = false === $php ? null : array_merge([$php], $executableFinder->findArguments());
        }

        if (null === $php) {
            throw new RuntimeException('Unable to find PHP binary.');
        }

        $tmpIni = $this->writeTmpIni($this->getAllIniFiles(), sys_get_temp_dir());

        $php = array_merge($php, ['-n', '-c', $tmpIni]);
        register_shutdown_function('unlink', $tmpIni);

        $command = array_merge($php, $command);

        parent::__construct($command, $cwd, $env, null, $timeout);
    }

    public static function fromShellCommandline(string $command, ?string $cwd = null, ?array $env = null, mixed $input = null, ?float $timeout = 60): static
    {
        throw new LogicException(\sprintf('The "%s()" method cannot be called when using "%s".', __METHOD__, self::class));
    }

    /**
     * @param (callable('out'|'err', string):void)|null $callback
     */
    public function start(?callable $callback = null, array $env = []): void
    {
        if (null === $this->getCommandLine()) {
            throw new RuntimeException('Unable to find the PHP executable.');
        }

        parent::start($callback, $env);
    }

    private function writeTmpIni(array $iniFiles, string $tmpDir): string
    {
        if (false === $tmpfile = @tempnam($tmpDir, '')) {
            throw new RuntimeException('Unable to create temporary ini file.');
        }

        // $iniFiles has at least one item and it may be empty
        if ('' === $iniFiles[0]) {
            array_shift($iniFiles);
        }

        $content = '';

        foreach ($iniFiles as $file) {
            // Check for inaccessible ini files
            if (($data = @file_get_contents($file)) === false) {
                throw new RuntimeException('Unable to read ini: '.$file);
            }
            // Check and remove directives after HOST and PATH sections
            if (preg_match('/^\s*\[(?:PATH|HOST)\s*=/mi', $data, $matches, \PREG_OFFSET_CAPTURE)) {
                $data = substr($data, 0, $matches[0][1]);
            }

            $content .= $data."\n";
        }

        // Merge loaded settings into our ini content, if it is valid
        $config = parse_ini_string($content);
        $loaded = ini_get_all(null, false);

        if (false === $config || false === $loaded) {
            throw new RuntimeException('Unable to parse ini data.');
        }

        $content .= $this->mergeLoadedConfig($loaded, $config);

        // Work-around for https://bugs.php.net/bug.php?id=75932
        $content .= "opcache.enable_cli=0\n";

        if (false === @file_put_contents($tmpfile, $content)) {
            throw new RuntimeException('Unable to write temporary ini file.');
        }

        return $tmpfile;
    }

    private function mergeLoadedConfig(array $loadedConfig, array $iniConfig): string
    {
        $content = '';

        foreach ($loadedConfig as $name => $value) {
            if (!\is_string($value)) {
                continue;
            }

            if (!isset($iniConfig[$name]) || $iniConfig[$name] !== $value) {
                // Double-quote escape each value
                $content .= $name.'="'.addcslashes($value, '\\"')."\"\n";
            }
        }

        return $content;
    }

    private function getAllIniFiles(): array
    {
        $paths = [(string) php_ini_loaded_file()];

        if (false !== $scanned = php_ini_scanned_files()) {
            $paths = array_merge($paths, array_map('trim', explode(',', $scanned)));
        }

        return $paths;
    }
}
