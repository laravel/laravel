<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject;

use function sprintf;
use RuntimeException;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class NeverReturningMethodException extends RuntimeException implements Exception
{
    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    public function __construct(string $className, string $methodName)
    {
        parent::__construct(
            sprintf(
                'Method %s::%s() is declared to never return',
                $className,
                $methodName,
            ),
        );
    }
}
