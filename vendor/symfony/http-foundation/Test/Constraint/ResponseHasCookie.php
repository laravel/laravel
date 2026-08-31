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
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

final class ResponseHasCookie extends Constraint
{
    public function __construct(
        private string $name,
        private string $path = '/',
        private ?string $domain = null,
    ) {
    }

    public function toString(): string
    {
        $str = \sprintf('has cookie "%s"', $this->name);
        if ('/' !== $this->path) {
            $str .= \sprintf(' with path "%s"', $this->path);
        }
        if ($this->domain) {
            $str .= \sprintf(' for domain "%s"', $this->domain);
        }

        return $str;
    }

    /**
     * @param Response $response
     */
    protected function matches($response): bool
    {
        return null !== $this->getCookie($response);
    }

    /**
     * @param Response $response
     */
    protected function failureDescription($response): string
    {
        return 'the Response '.$this->toString();
    }

    private function getCookie(Response $response): ?Cookie
    {
        $cookies = $response->headers->getCookies();

        $filteredCookies = array_filter($cookies, fn (Cookie $cookie) => $cookie->getName() === $this->name && $cookie->getPath() === $this->path && $cookie->getDomain() === $this->domain);

        return reset($filteredCookies) ?: null;
    }
}
