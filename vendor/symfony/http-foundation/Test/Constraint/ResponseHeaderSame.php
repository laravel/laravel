<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\HttpFoundation\Response;

final class ResponseHeaderSame extends Constraint
{
    public function __construct(
        private string $headerName,
        private string $expectedValue,
    ) {
    }

    public function toString(): string
    {
        return \sprintf('has header "%s" with value "%s"', $this->headerName, $this->expectedValue);
    }

    /**
     * @param Response $response
     */
    protected function matches($response): bool
    {
        return $this->expectedValue === $response->headers->get($this->headerName, null);
    }

    /**
     * @param Response $response
     */
    protected function failureDescription($response): string
    {
        return 'the Response '.$this->toString();
    }
}
