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

use function call_user_func_array;
use function func_get_args;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\Rule\AnyInvokedCount;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This trait is not covered by the backward compatibility promise for PHPUnit
 */
trait Method
{
    abstract public function __phpunit_getInvocationHandler(): InvocationHandler;

    public function method(): InvocationMocker
    {
        $expects = $this->__phpunit_getInvocationHandler()->expects(new AnyInvokedCount);

        return call_user_func_array(
            [$expects, 'method'],
            func_get_args(),
        );
    }
}
