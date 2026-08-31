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

final class ResponseStatusCodeSame extends Constraint
{
    public function __construct(
        private int $statusCode,
        private readonly bool $verbose = true,
    ) {
    }

    public function toString(): string
    {
        return 'status code is '.$this->statusCode;
    }

    /**
     * @param Response $response
     */
    protected function matches($response): bool
    {
        return $this->statusCode === $response->getStatusCode();
    }

    /**
     * @param Response $response
     */
    protected function failureDescription($response): string
    {
        return 'the Response '.$this->toString();
    }

    /**
     * @param Response $response
     */
    protected function additionalFailureDescription($response): string
    {
        return $this->verbose ? (string) $response : explode("\r\n\r\n", (string) $response)[0];
    }
}
