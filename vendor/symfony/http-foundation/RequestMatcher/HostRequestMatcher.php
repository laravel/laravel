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
 * Checks the Request URL host name matches a regular expression.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class HostRequestMatcher implements RequestMatcherInterface
{
    public function __construct(private string $regexp)
    {
    }

    public function matches(Request $request): bool
    {
        return preg_match('{'.$this->regexp.'}i', $request->getHost());
    }
}
