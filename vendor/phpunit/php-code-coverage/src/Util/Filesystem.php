<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Util;

use function dirname;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function sprintf;
use function str_contains;
use SebastianBergmann\CodeCoverage\Driver\WriteOperationFailedException;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class Filesystem
{
    /**
     * @throws DirectoryCouldNotBeCreatedException
     */
    public static function createDirectory(string $directory): void
    {
        $success = !(!is_dir($directory) && !@mkdir($directory, 0o777, true) && !is_dir($directory));

        if (!$success) {
            throw new DirectoryCouldNotBeCreatedException(
                sprintf(
                    'Directory "%s" could not be created',
                    $directory,
                ),
            );
        }
    }

    /**
     * @param non-empty-string $target
     *
     * @throws WriteOperationFailedException
     */
    public static function write(string $target, string $buffer): void
    {
        if (!str_contains($target, '://')) {
            self::createDirectory(dirname($target));
        }

        if (@file_put_contents($target, $buffer) === false) {
            throw new WriteOperationFailedException($target);
        }
    }
}
