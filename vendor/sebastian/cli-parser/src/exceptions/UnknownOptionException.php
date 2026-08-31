<?php declare(strict_types=1);
/*
 * This file is part of sebastian/cli-parser.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CliParser;

use function sprintf;
use RuntimeException;

final class UnknownOptionException extends RuntimeException implements Exception
{
    public function __construct(string $option)
    {
        parent::__construct(
            sprintf(
                'Unknown option "%s"',
                $option,
            ),
        );
    }
}
