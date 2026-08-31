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

use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

/**
 * ExpressionRequestMatcher uses an expression to match a Request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ExpressionRequestMatcher implements RequestMatcherInterface
{
    public function __construct(
        private ExpressionLanguage $language,
        private Expression|string $expression,
    ) {
    }

    public function matches(Request $request): bool
    {
        return $this->language->evaluate($this->expression, [
            'request' => $request,
            'method' => $request->getMethod(),
            'path' => rawurldecode($request->getPathInfo()),
            'host' => $request->getHost(),
            'ip' => $request->getClientIp(),
            'attributes' => $request->attributes->all(),
        ]);
    }
}
