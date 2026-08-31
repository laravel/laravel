<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\RateLimiter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\Policy\NoLimiter;
use Symfony\Component\RateLimiter\RateLimit;

/**
 * An implementation of PeekableRequestRateLimiterInterface that
 * fits most use-cases.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
abstract class AbstractRequestRateLimiter implements PeekableRequestRateLimiterInterface
{
    public function consume(Request $request): RateLimit
    {
        return $this->doConsume($request, 1);
    }

    public function peek(Request $request): RateLimit
    {
        return $this->doConsume($request, 0);
    }

    private function doConsume(Request $request, int $tokens): RateLimit
    {
        $limiters = $this->getLimiters($request);
        if (0 === \count($limiters)) {
            $limiters = [new NoLimiter()];
        }

        $minimalRateLimit = null;
        foreach ($limiters as $limiter) {
            $rateLimit = $limiter->consume($tokens);

            $minimalRateLimit = $minimalRateLimit ? self::getMinimalRateLimit($minimalRateLimit, $rateLimit) : $rateLimit;
        }

        return $minimalRateLimit;
    }

    public function reset(Request $request): void
    {
        foreach ($this->getLimiters($request) as $limiter) {
            $limiter->reset();
        }
    }

    /**
     * @return LimiterInterface[] a set of limiters using keys extracted from the request
     */
    abstract protected function getLimiters(Request $request): array;

    private static function getMinimalRateLimit(RateLimit $first, RateLimit $second): RateLimit
    {
        if ($first->isAccepted() !== $second->isAccepted()) {
            return $first->isAccepted() ? $second : $first;
        }

        $firstRemainingTokens = $first->getRemainingTokens();
        $secondRemainingTokens = $second->getRemainingTokens();

        if ($firstRemainingTokens === $secondRemainingTokens) {
            return $first->getRetryAfter() < $second->getRetryAfter() ? $second : $first;
        }

        return $firstRemainingTokens > $secondRemainingTokens ? $second : $first;
    }
}
