<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Metadata;

use function sprintf;
use PHPUnit\Exception;
use RuntimeException;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class AnnotationsAreNotSupportedForInternalClassesException extends RuntimeException implements Exception
{
    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        parent::__construct(
            sprintf(
                'Annotations can only be parsed for user-defined classes, trying to parse annotations for class "%s"',
                $className,
            ),
        );
    }
}
