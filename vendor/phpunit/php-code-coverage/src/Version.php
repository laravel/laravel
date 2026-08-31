<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage;

use function dirname;
use SebastianBergmann\Version as VersionId;

final class Version
{
    private static string $version = '';

    public static function id(): string
    {
        if (self::$version === '') {
            self::$version = (new VersionId('11.0.12', dirname(__DIR__)))->asString();
        }

        return self::$version;
    }
}
