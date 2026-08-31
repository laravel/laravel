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

use function sprintf;
use RuntimeException;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class InvalidVersionOperatorException extends RuntimeException implements Exception
{
    public function __construct(string $operator)
    {
        parent::__construct(
            sprintf(
                '"%s" is not a valid version_compare() operator',
                $operator,
            ),
        );
    }
}
