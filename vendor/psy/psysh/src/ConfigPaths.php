<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy;

/**
 * A Psy Shell configuration path helper.
 */
class ConfigPaths
{
    private ?string $configDir = null;
    private ?string $dataDir = null;
    private ?string $runtimeDir = null;
    private EnvInterface $env;

    /**
     * ConfigPaths constructor.
     *
     * Optionally provide `configDir`, `dataDir` and `runtimeDir` overrides.
     *
     * @see self::overrideDirs
     *
     * @param string[]          $overrides Directory overrides
     * @param EnvInterface|null $env
     */
    public function __construct(array $overrides = [], ?EnvInterface $env = null)
    {
        $this->overrideDirs($overrides);

        $this->env = $env ?: (\PHP_SAPI === 'cli-server' ? new SystemEnv() : new SuperglobalsEnv());
    }

    /**
     * Provide `configDir`, `dataDir` and `runtimeDir` overrides.
     *
     * If a key is set but empty, the override will be removed. If it is not set
     * at all, any existing override will persist.
     *
     * @param string[] $overrides Directory overrides
     */
    public function overrideDirs(array $overrides)
    {
        if (\array_key_exists('configDir', $overrides)) {
            $this->configDir = $overrides['configDir'] ?: null;
        }

        if (\array_key_exists('dataDir', $overrides)) {
            $this->dataDir = $overrides['dataDir'] ?: null;
        }

        if (\array_key_exists('runtimeDir', $overrides)) {
            $this->runtimeDir = $overrides['runtimeDir'] ?: null;
        }
    }

    /**
     * Get the current home directory.
     */
    public function homeDir(): ?string
    {
        if ($homeDir = $this->getEnv('HOME') ?: $this->windowsHomeDir()) {
            return \strtr($homeDir, '\\', '/');
        }

        return null;
    }

    private function windowsHomeDir(): ?string
    {
        if (\defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $homeDrive = $this->getEnv('HOMEDRIVE');
            $homePath = $this->getEnv('HOMEPATH');
            if ($homeDrive && $homePath) {
                return $homeDrive.'/'.$homePath;
            }
        }

        return null;
    }

    private function homeConfigDir(): ?string
    {
        if ($homeConfigDir = $this->getEnv('XDG_CONFIG_HOME')) {
            return $homeConfigDir;
        }

        $homeDir = $this->homeDir();
        if ($homeDir === null) {
            return null;
        }

        return $homeDir === '/' ? $homeDir.'.config' : $homeDir.'/.config';
    }

    /**
     * Get potential config directory paths.
     *
     * Returns `~/.psysh`, `%APPDATA%/PsySH` (when on Windows), and all
     * XDG Base Directory config directories:
     *
     *     http://standards.freedesktop.org/basedir-spec/basedir-spec-latest.html
     *
     * @return string[]
     */
    public function configDirs(): array
    {
        if ($this->configDir !== null) {
            return [$this->configDir];
        }

        $configDirs = $this->getEnvArray('XDG_CONFIG_DIRS') ?: ['/etc/xdg'];

        return $this->allDirNames(\array_merge([$this->homeConfigDir()], $configDirs));
    }

    /**
     * Get the current home config directory.
     *
     * Returns the highest precedence home config directory which actually
     * exists. If none of them exists, returns the highest precedence home
     * config directory (`%APPDATA%/PsySH` on Windows, `~/.config/psysh`
     * everywhere else).
     *
     * @see self::homeConfigDir
     */
    public function currentConfigDir(): ?string
    {
        if ($this->configDir !== null) {
            return $this->configDir;
        }

        $configDirs = $this->allDirNames([$this->homeConfigDir()]);

        foreach ($configDirs as $configDir) {
            if (@\is_dir($configDir)) {
                return $configDir;
            }
        }

        return $configDirs[0] ?? null;
    }

    /**
     * Find real config files in config directories.
     *
     * @param string[] $names Config file names
     *
     * @return string[]
     */
    public function configFiles(array $names): array
    {
        return $this->allRealFiles($this->configDirs(), $names);
    }

    /**
     * Get potential data directory paths.
     *
     * If a `dataDir` option was explicitly set, returns an array containing
     * just that directory.
     *
     * Otherwise, it returns `~/.psysh` and all XDG Base Directory data directories:
     *
     *     http://standards.freedesktop.org/basedir-spec/basedir-spec-latest.html
     *
     * @return string[]
     */
    public function dataDirs(): array
    {
        if ($this->dataDir !== null) {
            return [$this->dataDir];
        }

        $homeDataDir = $this->getEnv('XDG_DATA_HOME') ?: $this->homeDir().'/.local/share';
        $dataDirs = $this->getEnvArray('XDG_DATA_DIRS') ?: ['/usr/local/share', '/usr/share'];

        return $this->allDirNames(\array_merge([$homeDataDir], $dataDirs));
    }

    /**
     * Get the current home data directory.
     *
     * Returns the highest precedence home data directory which actually
     * exists and is writable. If none of them exists, returns the highest
     * precedence home data directory.
     */
    public function currentDataDir(): ?string
    {
        if ($this->dataDir !== null) {
            return $this->dataDir;
        }

        $dataDirs = $this->dataDirs();

        // Find first writable directory
        foreach ($dataDirs as $dir) {
            if (@\is_dir($dir) && @\is_writable($dir)) {
                return $dir;
            }
        }

        // Return first (user) directory even if it doesn't exist yet
        return $dataDirs[0] ?? null;
    }

    /**
     * Get the local config root directory (cwd only, no ancestor walking).
     *
     * Used for local `.psysh.php` config file detection. Returns the current
     * working directory, or null if getcwd() fails.
     */
    public function localConfigRoot(): ?string
    {
        $cwd = \getcwd();
        if ($cwd === false) {
            return null;
        }

        return \strtr($cwd, '\\', '/');
    }

    /**
     * Find a project root for trust decisions.
     *
     * Walks up ancestors to find the nearest composer.json or composer.lock.
     * If none found, falls back to the nearest .psysh.php, then to the current
     * working directory.
     *
     * Used for trust decisions on Composer autoload and project-level features.
     */
    public function projectRoot(?string $cwd = null): ?string
    {
        $cwd = $cwd ?? \getcwd();
        if ($cwd === false) {
            return null;
        }

        $dir = \strtr($cwd, '\\', '/');
        $root = null;
        $localConfigRoot = null;

        $current = $dir;
        $parent = \dirname($current);

        while ($current !== $parent) {
            if ($root === null && (@\is_file($current.'/composer.json') || @\is_file($current.'/composer.lock'))) {
                $root = $current;
            }

            if ($localConfigRoot === null && @\is_file($current.'/.psysh.php')) {
                $localConfigRoot = $current;
            }

            $current = $parent;
            $parent = \dirname($current);
        }

        if ($root !== null) {
            return $root;
        }

        if ($localConfigRoot !== null) {
            return $localConfigRoot;
        }

        return $dir;
    }

    /**
     * Find real data files in config directories.
     *
     * @param string[] $names Config file names
     *
     * @return string[]
     */
    public function dataFiles(array $names): array
    {
        return $this->allRealFiles($this->dataDirs(), $names);
    }

    /**
     * Get a runtime directory.
     *
     * Defaults to `/psysh` inside the system's temp dir.
     */
    public function runtimeDir(): string
    {
        if ($this->runtimeDir !== null) {
            return $this->runtimeDir;
        }

        // Fallback to a boring old folder in the system temp dir.
        $runtimeDir = $this->getEnv('XDG_RUNTIME_DIR') ?: \sys_get_temp_dir();

        return \strtr($runtimeDir, '\\', '/').'/psysh';
    }

    /**
     * Get a list of directories in PATH.
     *
     * If $PATH is unset/empty it defaults to '/usr/sbin:/usr/bin:/sbin:/bin'.
     *
     * @return string[]
     */
    public function pathDirs(): array
    {
        return $this->getEnvArray('PATH') ?: ['/usr/sbin', '/usr/bin', '/sbin', '/bin'];
    }

    /**
     * Locate a command (an executable) in $PATH.
     *
     * Behaves like 'command -v COMMAND' or 'which COMMAND'.
     * If $PATH is unset/empty it defaults to '/usr/sbin:/usr/bin:/sbin:/bin'.
     *
     * @param string $command the executable to locate
     */
    public function which($command): ?string
    {
        if (!\is_string($command) || $command === '') {
            return null;
        }

        foreach ($this->pathDirs() as $path) {
            $fullpath = $path.\DIRECTORY_SEPARATOR.$command;
            if (@\is_file($fullpath) && @\is_executable($fullpath)) {
                return $fullpath;
            }
        }

        return null;
    }

    /**
     * Get all PsySH directory name candidates given a list of base directories.
     *
     * This expects that XDG-compatible directory paths will be passed in.
     * `psysh` will be added to each of $baseDirs, and we'll throw in `~/.psysh`
     * and a couple of Windows-friendly paths as well.
     *
     * @param string[] $baseDirs base directory paths
     *
     * @return string[]
     */
    private function allDirNames(array $baseDirs): array
    {
        $baseDirs = \array_filter($baseDirs);
        $dirs = \array_map(fn ($dir) => \strtr($dir, '\\', '/').'/psysh', $baseDirs);

        // Add ~/.psysh
        if ($home = $this->getEnv('HOME')) {
            $dirs[] = \strtr($home, '\\', '/').'/.psysh';
        }

        // Add some Windows specific ones :)
        if (\defined('PHP_WINDOWS_VERSION_MAJOR')) {
            if ($appData = $this->getEnv('APPDATA')) {
                // AppData gets preference
                \array_unshift($dirs, \strtr($appData, '\\', '/').'/PsySH');
            }

            if ($windowsHomeDir = $this->windowsHomeDir()) {
                $dir = \strtr($windowsHomeDir, '\\', '/').'/.psysh';
                if (!\in_array($dir, $dirs)) {
                    $dirs[] = $dir;
                }
            }
        }

        return $dirs;
    }

    /**
     * Given a list of directories, and a list of filenames, find the ones that
     * are real files.
     *
     * @return string[]
     */
    private function allRealFiles(array $dirNames, array $fileNames): array
    {
        $files = [];
        foreach ($dirNames as $dir) {
            foreach ($fileNames as $name) {
                $file = $dir.'/'.$name;
                if (@\is_file($file)) {
                    $files[] = $file;
                }
            }
        }

        return $files;
    }

    /**
     * Make a path prettier by replacing cwd with . or home directory with ~.
     *
     * @param string|mixed $path       Path to prettify
     * @param string|null  $relativeTo Directory to make path relative to (defaults to cwd)
     * @param string|null  $homeDir    Home directory to replace with ~ (defaults to actual home)
     *
     * @return string|mixed Pretty path, or original value if not a string
     */
    public static function prettyPath($path, ?string $relativeTo = null, ?string $homeDir = null)
    {
        if (!\is_string($path)) {
            return $path;
        }

        $path = \strtr($path, '\\', '/');

        // Try replacing relativeTo directory first (more specific)
        $relativeTo = $relativeTo ?: \getcwd();
        if ($relativeTo !== false) {
            $relativeTo = \rtrim(\strtr($relativeTo, '\\', '/'), '/').'/';
            if (\strpos($path, $relativeTo) === 0) {
                return './'.\substr($path, \strlen($relativeTo));
            }
        }

        // Fall back to replacing home directory
        $homeDir = $homeDir ?: (new self())->homeDir();
        if ($homeDir && $homeDir !== '/') {
            $homeDir = \rtrim(\strtr($homeDir, '\\', '/'), '/').'/';
            if (\strpos($path, $homeDir) === 0) {
                return '~/'.\substr($path, \strlen($homeDir));
            }
        }

        return $path;
    }

    /**
     * Ensure that $dir exists and is writable.
     *
     * Generates E_USER_NOTICE error if the directory is not writable or creatable.
     *
     * @param string $dir
     *
     * @return bool False if directory exists but is not writeable, or cannot be created
     */
    public static function ensureDir(string $dir): bool
    {
        if (!\is_dir($dir)) {
            // Just try making it and see if it works
            @\mkdir($dir, 0700, true);
        }

        if (!\is_dir($dir) || !\is_writable($dir)) {
            \trigger_error(\sprintf('Writing to directory %s is not allowed.', $dir), \E_USER_NOTICE);

            return false;
        }

        return true;
    }

    /**
     * Ensure that $file exists and is writable, make the parent directory if necessary.
     *
     * Generates E_USER_NOTICE error if either $file or its directory is not writable.
     *
     * @param string $file
     *
     * @return string|false Full path to $file, or false if file is not writable
     */
    public static function touchFileWithMkdir(string $file)
    {
        if (\file_exists($file)) {
            if (\is_writable($file)) {
                return $file;
            }

            \trigger_error(\sprintf('Writing to %s is not allowed.', $file), \E_USER_NOTICE);

            return false;
        }

        if (!self::ensureDir(\dirname($file))) {
            return false;
        }

        \touch($file);

        return $file;
    }

    private function getEnv(string $key)
    {
        return $this->env->get($key);
    }

    private function getEnvArray(string $key)
    {
        if ($value = $this->getEnv($key)) {
            return \explode(\PATH_SEPARATOR, $value);
        }

        return null;
    }
}
