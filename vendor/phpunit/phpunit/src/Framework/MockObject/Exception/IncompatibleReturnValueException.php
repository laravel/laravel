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

use function get_debug_type;
use function sprintf;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class IncompatibleReturnValueException extends \PHPUnit\Framework\Exception implements Exception
{
    public function __construct(ConfigurableMethod $method, mixed $value)
    {
        parent::__construct(
            sprintf(
                'Method %s may not return value of type %s, its declared return type is "%s"',
                $method->name(),
                get_debug_type($value),
                $method->returnTypeDeclaration(),
            ),
        );
    }
}
