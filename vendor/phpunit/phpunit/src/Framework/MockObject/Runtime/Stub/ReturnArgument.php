<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject\Stub;

use PHPUnit\Framework\MockObject\Invocation;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ReturnArgument implements Stub
{
    private int $argumentIndex;

    public function __construct(int $argumentIndex)
    {
        $this->argumentIndex = $argumentIndex;
    }

    public function invoke(Invocation $invocation): mixed
    {
        return $invocation->parameters()[$this->argumentIndex] ?? null;
    }
}
