<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\RequestMatcher;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

/**
 * Checks the Request attributes matches all regular expressions.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AttributesRequestMatcher implements RequestMatcherInterface
{
    /**
     * @param array<string, string> $regexps
     */
    public function __construct(private array $regexps)
    {
    }

    public function matches(Request $request): bool
    {
        foreach ($this->regexps as $key => $regexp) {
            $attribute = $request->attributes->get($key);
            if (!\is_string($attribute)) {
                return false;
            }
            if (!preg_match('{'.$regexp.'}', $attribute)) {
                return false;
            }
        }

        return true;
    }
}
