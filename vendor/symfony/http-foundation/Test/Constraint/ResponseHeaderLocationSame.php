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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ResponseHeaderLocationSame extends Constraint
{
    public function __construct(private Request $request, private string $expectedValue)
    {
    }

    public function toString(): string
    {
        return \sprintf('has header "Location" matching "%s"', $this->expectedValue);
    }

    protected function matches($other): bool
    {
        if (!$other instanceof Response) {
            return false;
        }

        $location = $other->headers->get('Location');

        if (null === $location) {
            return false;
        }

        return $this->toFullUrl($this->expectedValue) === $this->toFullUrl($location);
    }

    protected function failureDescription($other): string
    {
        return 'the Response '.$this->toString();
    }

    private function toFullUrl(string $url): string
    {
        if (null === parse_url($url, \PHP_URL_PATH)) {
            $url .= '/';
        }

        if (str_starts_with($url, '//')) {
            return \sprintf('%s:%s', $this->request->getScheme(), $url);
        }

        if (str_starts_with($url, '/')) {
            return $this->request->getSchemeAndHttpHost().$url;
        }

        return $url;
    }
}
