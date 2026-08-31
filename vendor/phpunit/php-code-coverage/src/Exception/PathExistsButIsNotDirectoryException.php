<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Driver;

use function sprintf;
use RuntimeException;
use SebastianBergmann\CodeCoverage\Exception;

final class PathExistsButIsNotDirectoryException extends RuntimeException implements Exception
{
    public function __construct(string $path)
    {
        parent::__construct(sprintf('"%s" exists but is not a directory', $path));
    }
}
