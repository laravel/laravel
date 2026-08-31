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

use const PHP_BINARY;
use const PHP_SAPI;
use const PHP_VERSION;
use function array_map;
use function array_merge;
use function assert;
use function escapeshellarg;
use function explode;
use function extension_loaded;
use function in_array;
use function ini_get;
use function is_array;
use function parse_ini_file;
use function php_ini_loaded_file;
use function php_ini_scanned_files;
use function phpversion;
use function sprintf;
use function strrpos;
use function version_compare;
use function xdebug_info;

final class Runtime
{
    /**
     * Returns true when Xdebug or PCOV is available or
     * the runtime used is PHPDBG.
     */
    public function canCollectCodeCoverage(): bool
    {
        if ($this->hasPHPDBGCodeCoverage()) {
            return true;
        }

        if ($this->hasPCOV()) {
            return true;
        }

        if (!$this->hasXdebug()) {
            return false;
        }

        $xdebugVersion = phpversion('xdebug');

        assert($xdebugVersion !== false);

        if (version_compare($xdebugVersion, '3', '<')) {
            return true;
        }

        $xdebugMode = xdebug_info('mode');

        assert(is_array($xdebugMode));

        if (in_array('coverage', $xdebugMode, true)) {
            return true;
        }

        return false;
    }

    /**
     * Returns true when Zend OPcache is loaded, enabled,
     * and is configured to discard comments.
     */
    public function discardsComments(): bool
    {
        if (!$this->isOpcacheActive()) {
            return false;
        }

        if (ini_get('opcache.save_comments') !== '0') {
            return false;
        }

        return true;
    }

    /**
     * Returns true when Zend OPcache is loaded, enabled,
     * and is configured to perform just-in-time compilation.
     */
    public function performsJustInTimeCompilation(): bool
    {
        if (!$this->isOpcacheActive()) {
            return false;
        }

        if (ini_get('opcache.jit_buffer_size') === '0') {
            return false;
        }

        $jit = (string) ini_get('opcache.jit');

        if (($jit === 'disable') || ($jit === 'off')) {
            return false;
        }

        if (strrpos($jit, '0') === 3) {
            return false;
        }

        return true;
    }

    /**
     * Returns the raw path to the binary of the current runtime.
     *
     * @deprecated
     */
    public function getRawBinary(): string
    {
        return PHP_BINARY;
    }

    /**
     * Returns the escaped path to the binary of the current runtime.
     *
     * @deprecated
     */
    public function getBinary(): string
    {
        return escapeshellarg(PHP_BINARY);
    }

    public function getNameWithVersion(): string
    {
        return $this->getName() . ' ' . $this->getVersion();
    }

    public function getNameWithVersionAndCodeCoverageDriver(): string
    {
        if ($this->hasPCOV()) {
            return sprintf(
                '%s with PCOV %s',
                $this->getNameWithVersion(),
                phpversion('pcov'),
            );
        }

        if ($this->hasXdebug()) {
            return sprintf(
                '%s with Xdebug %s',
                $this->getNameWithVersion(),
                phpversion('xdebug'),
            );
        }

        return $this->getNameWithVersion();
    }

    public function getName(): string
    {
        if ($this->isPHPDBG()) {
            // @codeCoverageIgnoreStart
            return 'PHPDBG';
            // @codeCoverageIgnoreEnd
        }

        return 'PHP';
    }

    public function getVendorUrl(): string
    {
        return 'https://www.php.net/';
    }

    public function getVersion(): string
    {
        return PHP_VERSION;
    }

    /**
     * Returns true when the runtime used is PHP and Xdebug is loaded.
     */
    public function hasXdebug(): bool
    {
        return $this->isPHP() && extension_loaded('xdebug');
    }

    /**
     * Returns true when the runtime used is PHP without the PHPDBG SAPI.
     */
    public function isPHP(): bool
    {
        return !$this->isPHPDBG();
    }

    /**
     * Returns true when the runtime used is PHP with the PHPDBG SAPI.
     */
    public function isPHPDBG(): bool
    {
        return PHP_SAPI === 'phpdbg';
    }

    /**
     * Returns true when the runtime used is PHP with the PHPDBG SAPI
     * and the phpdbg_*_oplog() functions are available (PHP >= 7.0).
     */
    public function hasPHPDBGCodeCoverage(): bool
    {
        return $this->isPHPDBG();
    }

    /**
     * Returns true when the runtime used is PHP with PCOV loaded and enabled.
     */
    public function hasPCOV(): bool
    {
        return $this->isPHP() && extension_loaded('pcov') && ini_get('pcov.enabled');
    }

    /**
     * Parses the loaded php.ini file (if any) as well as all
     * additional php.ini files from the additional ini dir for
     * a list of all configuration settings loaded from files
     * at startup. Then checks for each php.ini setting passed
     * via the `$values` parameter whether this setting has
     * been changed at runtime. Returns an array of strings
     * where each string has the format `key=value` denoting
     * the name of a changed php.ini setting with its new value.
     *
     * @param list<string> $values
     *
     * @return array<string, string>
     */
    public function getCurrentSettings(array $values): array
    {
        $diff  = [];
        $files = [];

        if ($file = php_ini_loaded_file()) {
            $files[] = $file;
        }

        if ($scanned = php_ini_scanned_files()) {
            $files = array_merge(
                $files,
                array_map(
                    'trim',
                    explode(",\n", $scanned),
                ),
            );
        }

        foreach ($files as $ini) {
            $config = parse_ini_file($ini, true);

            foreach ($values as $value) {
                $set = ini_get($value);

                if (empty($set)) {
                    continue;
                }

                if ((!isset($config[$value]) || ($set !== $config[$value]))) {
                    $diff[$value] = sprintf('%s=%s', $value, $set);
                }
            }
        }

        return $diff;
    }

    private function isOpcacheActive(): bool
    {
        if (!extension_loaded('Zend OPcache')) {
            return false;
        }

        if ((PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') && ini_get('opcache.enable_cli') === '1') {
            return true;
        }

        if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg' && ini_get('opcache.enable') === '1') {
            return true;
        }

        return false;
    }
}
