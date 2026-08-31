<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util;

use const PHP_MAJOR_VERSION;
use const PHP_MINOR_VERSION;
use function array_keys;
use function array_reverse;
use function array_shift;
use function assert;
use function defined;
use function get_defined_constants;
use function get_included_files;
use function in_array;
use function ini_get_all;
use function is_array;
use function is_file;
use function is_scalar;
use function preg_match;
use function serialize;
use function sprintf;
use function str_ends_with;
use function str_starts_with;
use function strtr;
use function var_export;
use Closure;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class GlobalState
{
    /**
     * @var list<string>
     */
    private const SUPER_GLOBAL_ARRAYS = [
        '_ENV',
        '_POST',
        '_GET',
        '_COOKIE',
        '_SERVER',
        '_FILES',
        '_REQUEST',
    ];

    /**
     * @var array<string, array<string, true>>
     */
    private const DEPRECATED_INI_SETTINGS = [
        '7.3' => [
            'iconv.input_encoding'       => true,
            'iconv.output_encoding'      => true,
            'iconv.internal_encoding'    => true,
            'mbstring.func_overload'     => true,
            'mbstring.http_input'        => true,
            'mbstring.http_output'       => true,
            'mbstring.internal_encoding' => true,
            'string.strip_tags'          => true,
        ],

        '7.4' => [
            'iconv.input_encoding'       => true,
            'iconv.output_encoding'      => true,
            'iconv.internal_encoding'    => true,
            'mbstring.func_overload'     => true,
            'mbstring.http_input'        => true,
            'mbstring.http_output'       => true,
            'mbstring.internal_encoding' => true,
            'pdo_odbc.db2_instance_name' => true,
            'string.strip_tags'          => true,
        ],

        '8.0' => [
            'iconv.input_encoding'       => true,
            'iconv.output_encoding'      => true,
            'iconv.internal_encoding'    => true,
            'mbstring.http_input'        => true,
            'mbstring.http_output'       => true,
            'mbstring.internal_encoding' => true,
        ],

        '8.1' => [
            'auto_detect_line_endings'     => true,
            'filter.default'               => true,
            'iconv.input_encoding'         => true,
            'iconv.output_encoding'        => true,
            'iconv.internal_encoding'      => true,
            'mbstring.http_input'          => true,
            'mbstring.http_output'         => true,
            'mbstring.internal_encoding'   => true,
            'oci8.old_oci_close_semantics' => true,
        ],

        '8.2' => [
            'auto_detect_line_endings'     => true,
            'filter.default'               => true,
            'iconv.input_encoding'         => true,
            'iconv.output_encoding'        => true,
            'iconv.internal_encoding'      => true,
            'mbstring.http_input'          => true,
            'mbstring.http_output'         => true,
            'mbstring.internal_encoding'   => true,
            'oci8.old_oci_close_semantics' => true,
        ],

        '8.3' => [
            'auto_detect_line_endings'     => true,
            'filter.default'               => true,
            'iconv.input_encoding'         => true,
            'iconv.output_encoding'        => true,
            'iconv.internal_encoding'      => true,
            'mbstring.http_input'          => true,
            'mbstring.http_output'         => true,
            'mbstring.internal_encoding'   => true,
            'oci8.old_oci_close_semantics' => true,
        ],

        '8.4' => [
            'auto_detect_line_endings'     => true,
            'filter.default'               => true,
            'iconv.input_encoding'         => true,
            'iconv.output_encoding'        => true,
            'iconv.internal_encoding'      => true,
            'mbstring.http_input'          => true,
            'mbstring.http_output'         => true,
            'mbstring.internal_encoding'   => true,
            'oci8.old_oci_close_semantics' => true,
        ],

        '8.5' => [
            'auto_detect_line_endings'     => true,
            'filter.default'               => true,
            'iconv.input_encoding'         => true,
            'iconv.output_encoding'        => true,
            'iconv.internal_encoding'      => true,
            'mbstring.http_input'          => true,
            'mbstring.http_output'         => true,
            'mbstring.internal_encoding'   => true,
            'oci8.old_oci_close_semantics' => true,
        ],

        '8.6' => [
            'auto_detect_line_endings'     => true,
            'filter.default'               => true,
            'iconv.input_encoding'         => true,
            'iconv.output_encoding'        => true,
            'iconv.internal_encoding'      => true,
            'mbstring.http_input'          => true,
            'mbstring.http_output'         => true,
            'mbstring.internal_encoding'   => true,
            'oci8.old_oci_close_semantics' => true,
        ],
    ];

    /**
     * @throws Exception
     */
    public static function getIncludedFilesAsString(): string
    {
        return self::processIncludedFilesAsString(get_included_files());
    }

    /**
     * @param list<string> $files
     *
     * @throws Exception
     */
    public static function processIncludedFilesAsString(array $files): string
    {
        $excludeList = new ExcludeList;
        $prefix      = false;
        $result      = '';

        if (defined('__PHPUNIT_PHAR__')) {
            // @codeCoverageIgnoreStart
            $prefix = 'phar://' . __PHPUNIT_PHAR__ . '/';
            // @codeCoverageIgnoreEnd
        }

        // Do not process bootstrap script
        array_shift($files);

        // If bootstrap script was a Composer bin proxy, skip the second entry as well
        if (str_ends_with(strtr($files[0], '\\', '/'), '/phpunit/phpunit/phpunit')) {
            // @codeCoverageIgnoreStart
            array_shift($files);
            // @codeCoverageIgnoreEnd
        }

        foreach (array_reverse($files) as $file) {
            if (!empty($GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST']) &&
                in_array($file, $GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST'], true)) {
                continue;
            }

            if ($prefix !== false && str_starts_with($file, $prefix)) {
                continue;
            }

            // Skip virtual file system protocols
            if (preg_match('/^(vfs|phpvfs[a-z0-9]+):/', $file)) {
                continue;
            }

            if (!$excludeList->isExcluded($file) && is_file($file)) {
                $result = 'require_once \'' . $file . "';\n" . $result;
            }
        }

        return $result;
    }

    public static function getIniSettingsAsString(): string
    {
        $result = '';

        $iniSettings = ini_get_all(null, false);

        assert($iniSettings !== false);

        foreach ($iniSettings as $key => $value) {
            if (self::isIniSettingDeprecated($key)) {
                continue;
            }

            $result .= sprintf(
                '@ini_set(%s, %s);' . "\n",
                self::exportVariable($key),
                self::exportVariable((string) $value),
            );
        }

        return $result;
    }

    public static function getConstantsAsString(): string
    {
        $constants = get_defined_constants(true);
        $result    = '';

        if (isset($constants['user'])) {
            foreach ($constants['user'] as $name => $value) {
                $result .= sprintf(
                    'if (!defined(\'%s\')) define(\'%s\', %s);' . "\n",
                    $name,
                    $name,
                    self::exportVariable($value),
                );
            }
        }

        return $result;
    }

    public static function getGlobalsAsString(): string
    {
        $result = '';

        foreach (self::SUPER_GLOBAL_ARRAYS as $superGlobalArray) {
            if (isset($GLOBALS[$superGlobalArray]) && is_array($GLOBALS[$superGlobalArray])) {
                foreach (array_keys($GLOBALS[$superGlobalArray]) as $key) {
                    if ($GLOBALS[$superGlobalArray][$key] instanceof Closure) {
                        continue;
                    }

                    $result .= sprintf(
                        '$GLOBALS[\'%s\'][\'%s\'] = %s;' . "\n",
                        $superGlobalArray,
                        $key,
                        self::exportVariable($GLOBALS[$superGlobalArray][$key]),
                    );
                }
            }
        }

        $excludeList   = self::SUPER_GLOBAL_ARRAYS;
        $excludeList[] = 'GLOBALS';

        foreach (array_keys($GLOBALS) as $key) {
            if (!$GLOBALS[$key] instanceof Closure && !in_array($key, $excludeList, true)) {
                $result .= sprintf(
                    '$GLOBALS[\'%s\'] = %s;' . "\n",
                    $key,
                    self::exportVariable($GLOBALS[$key]),
                );
            }
        }

        return $result;
    }

    private static function exportVariable(mixed $variable): string
    {
        if (is_scalar($variable) || $variable === null ||
            (is_array($variable) && self::arrayOnlyContainsScalars($variable))) {
            return var_export($variable, true);
        }

        return 'unserialize(' . var_export(serialize($variable), true) . ')';
    }

    /**
     * @param array<mixed> $array
     */
    private static function arrayOnlyContainsScalars(array $array): bool
    {
        $result = true;

        foreach ($array as $element) {
            if (is_array($element)) {
                $result = self::arrayOnlyContainsScalars($element);
            } elseif (!is_scalar($element) && $element !== null) {
                $result = false;
            }

            if (!$result) {
                break;
            }
        }

        return $result;
    }

    private static function isIniSettingDeprecated(string $iniSetting): bool
    {
        return isset(self::DEPRECATED_INI_SETTINGS[PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION][$iniSetting]);
    }
}
