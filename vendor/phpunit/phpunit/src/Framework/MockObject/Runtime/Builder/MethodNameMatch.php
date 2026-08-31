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

use PHPUnit\Framework\Constraint\Constraint;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This interface is not covered by the backward compatibility promise for PHPUnit
 */
interface MethodNameMatch extends ParametersMatch
{
    /**
     * Adds a new method name match and returns the parameter match object for
     * further matching possibilities.
     */
    public function method(Constraint|string $constraint): self;
}
