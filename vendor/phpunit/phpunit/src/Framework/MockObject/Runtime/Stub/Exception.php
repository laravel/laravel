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
use Throwable;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Exception implements Stub
{
    private Throwable $exception;

    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @throws Throwable
     */
    public function invoke(Invocation $invocation): never
    {
        throw $this->exception;
    }
}
