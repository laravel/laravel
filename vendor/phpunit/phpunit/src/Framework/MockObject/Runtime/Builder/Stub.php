<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject\Builder;

use PHPUnit\Framework\MockObject\Stub\Stub as BaseStub;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This interface is not covered by the backward compatibility promise for PHPUnit
 */
interface Stub extends Identity
{
    /**
     * Stubs the matching method with the stub object $stub. Any invocations of
     * the matched method will now be handled by the stub instead.
     */
    public function will(BaseStub $stub): Identity;
}
