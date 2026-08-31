<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
enum NativeType: string
{
    case Array          = 'array';
    case Bool           = 'bool';
    case Callable       = 'callable';
    case ClosedResource = 'resource (closed)';
    case Float          = 'float';
    case Int            = 'int';
    case Iterable       = 'iterable';
    case Null           = 'null';
    case Numeric        = 'numeric';
    case Object         = 'object';
    case Resource       = 'resource';
    case Scalar         = 'scalar';
    case String         = 'string';
}
