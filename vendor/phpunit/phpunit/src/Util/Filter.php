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

use function array_unshift;
use function defined;
use function in_array;
use function is_file;
use function realpath;
use function sprintf;
use function str_starts_with;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\PhptAssertionFailedError;
use Throwable;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Filter
{
    /**
     * @throws Exception
     */
    public static function stackTraceFromThrowableAsString(Throwable $t, bool $unwrap = true): string
    {
        if ($t instanceof PhptAssertionFailedError) {
            $stackTrace = $t->syntheticTrace();
            $file       = $t->syntheticFile();
            $line       = $t->syntheticLine();
        } elseif ($t instanceof Exception) {
            $stackTrace = $t->getSerializableTrace();
            $file       = $t->getFile();
            $line       = $t->getLine();
        } else {
            if ($unwrap && $t->getPrevious()) {
                $t = $t->getPrevious();
            }

            $stackTrace = $t->getTrace();
            $file       = $t->getFile();
            $line       = $t->getLine();
        }

        if (!self::frameExists($stackTrace, $file, $line)) {
            array_unshift(
                $stackTrace,
                ['file' => $file, 'line' => $line],
            );
        }

        return self::stackTraceAsString($stackTrace);
    }

    /**
     * @param list<array{function?: string, line?: int, file?: string, class?: class-string, type?: string, args?: list<mixed>, object?: object}> $frames
     */
    public static function stackTraceAsString(array $frames): string
    {
        $buffer      = '';
        $prefix      = defined('__PHPUNIT_PHAR_ROOT__') ? __PHPUNIT_PHAR_ROOT__ : false;
        $excludeList = new ExcludeList;

        foreach ($frames as $frame) {
            if (self::shouldPrintFrame($frame, $prefix, $excludeList)) {
                $buffer .= sprintf(
                    "%s:%s\n",
                    $frame['file'],
                    $frame['line'] ?? '?',
                );
            }
        }

        return $buffer;
    }

    /**
     * @param array{function?: string, line?: int, file?: string, class?: class-string, type?: string, args?: list<mixed>, object?: object} $frame
     */
    private static function shouldPrintFrame(array $frame, false|string $prefix, ExcludeList $excludeList): bool
    {
        if (!isset($frame['file'])) {
            return false;
        }

        $file              = $frame['file'];
        $fileIsNotPrefixed = $prefix === false || !str_starts_with($file, $prefix);

        // @see https://github.com/sebastianbergmann/phpunit/issues/4033
        if (isset($GLOBALS['_SERVER']['SCRIPT_NAME'])) {
            $script = realpath($GLOBALS['_SERVER']['SCRIPT_NAME']);
        } else {
            // @codeCoverageIgnoreStart
            $script = '';
            // @codeCoverageIgnoreEnd
        }

        return $fileIsNotPrefixed &&
               $file !== $script &&
               self::fileIsExcluded($file, $excludeList) &&
               is_file($file);
    }

    private static function fileIsExcluded(string $file, ExcludeList $excludeList): bool
    {
        return (empty($GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST']) ||
                !in_array($file, $GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST'], true)) &&
                !$excludeList->isExcluded($file);
    }

    /**
     * @param list<array{function?: string, line?: int, file?: string, class?: class-string, type?: string, args?: list<mixed>, object?: object}> $trace
     */
    private static function frameExists(array $trace, string $file, int $line): bool
    {
        foreach ($trace as $frame) {
            if (isset($frame['file'], $frame['line']) && $frame['file'] === $file && $frame['line'] === $line) {
                return true;
            }
        }

        return false;
    }
}
