<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri;

use BackedEnum;
use League\Uri\Contracts\Conditionable;
use League\Uri\Contracts\FragmentDirective;
use League\Uri\Contracts\Transformable;
use League\Uri\Contracts\UriComponentInterface;
use League\Uri\Exceptions\SyntaxError;
use SensitiveParameter;
use Stringable;
use Throwable;
use TypeError;
use Uri\Rfc3986\Uri as Rfc3986Uri;
use Uri\WhatWg\Url as WhatWgUrl;

use function is_bool;
use function str_replace;
use function strpos;

final class Builder implements Conditionable, Transformable
{
    private ?string $scheme = null;
    private ?string $username = null;
    private ?string $password = null;
    private ?string $host = null;
    private ?int $port = null;
    private ?string $path = null;
    private ?string $query = null;
    private ?string $fragment = null;

    public function __construct(
        BackedEnum|Stringable|string|null $scheme = null,
        BackedEnum|Stringable|string|null $username = null,
        #[SensitiveParameter] BackedEnum|Stringable|string|null $password = null,
        BackedEnum|Stringable|string|null $host = null,
        BackedEnum|int|null $port = null,
        BackedEnum|Stringable|string|null $path = null,
        BackedEnum|Stringable|string|null $query = null,
        BackedEnum|Stringable|string|null $fragment = null,
    ) {
        $this
            ->scheme($scheme)
            ->userInfo($username, $password)
            ->host($host)
            ->port($port)
            ->path($path)
            ->query($query)
            ->fragment($fragment);
    }

    /**
     * @throws SyntaxError
     */
    public function scheme(BackedEnum|Stringable|string|null $scheme): self
    {
        $scheme = $this->filterString($scheme);
        if ($scheme !== $this->scheme) {
            UriString::isValidScheme($scheme) || throw new SyntaxError('The scheme `'.$scheme.'` is invalid.');

            $this->scheme = $scheme;
        }

        return $this;
    }

    /**
     * @throws SyntaxError
     */
    public function userInfo(
        BackedEnum|Stringable|string|null $user,
        #[SensitiveParameter] BackedEnum|Stringable|string|null $password = null
    ): static {
        $username = Encoder::encodeUser($this->filterString($user));
        $password = Encoder::encodePassword($this->filterString($password));
        if ($username !== $this->username || $password !== $this->password) {
            $this->username = $username;
            $this->password = $password;
        }

        return $this;
    }

    /**
     * @throws SyntaxError
     */
    public function host(BackedEnum|Stringable|string|null $host): self
    {
        $host = $this->filterString($host);
        if ($host !== $this->host) {
            null === $host
            || HostRecord::isValid($host)
            || throw new SyntaxError('The host `'.$host.'` is invalid.');

            $this->host = $host;
        }

        return $this;
    }

    /**
     * @throws SyntaxError
     * @throws TypeError
     */
    public function port(BackedEnum|int|null $port): self
    {
        if ($port instanceof BackedEnum) {
            1 === preg_match('/^\d+$/', (string) $port->value)
            || throw new TypeError('The port must be a valid BackedEnum containing a number.');

            $port = (int) $port->value;
        }

        if ($port !== $this->port) {
            null === $port
            || ($port >= 0 && $port < 65535)
            || throw new SyntaxError('The port value must be null or an integer between 0 and 65535.');

            $this->port = $port;
        }

        return $this;
    }

    /**
     * @throws SyntaxError
     */
    public function authority(BackedEnum|Stringable|string|null $authority): self
    {
        ['user' => $user, 'pass' => $pass, 'host' => $host, 'port' => $port] = UriString::parseAuthority($authority);

        return $this
            ->userInfo($user, $pass)
            ->host($host)
            ->port($port);
    }

    /**
     * @throws SyntaxError
     */
    public function path(BackedEnum|Stringable|string|null $path): self
    {
        $path = $this->filterString($path);
        if ($path !== $this->path) {
            $this->path = null !== $path ? Encoder::encodePath($path) : null;
        }

        return $this;
    }

    /**
     * @throws SyntaxError
     */
    public function query(BackedEnum|Stringable|string|null $query): self
    {
        $query = $this->filterString($query);
        if ($query !== $this->query) {
            $this->query = Encoder::encodeQueryOrFragment($query);
        }

        return $this;
    }

    /**
     * @throws SyntaxError
     */
    public function fragment(BackedEnum|Stringable|string|null $fragment): self
    {
        $fragment = $this->filterString($fragment);
        if ($fragment !== $this->fragment) {
            $this->fragment = Encoder::encodeQueryOrFragment($fragment);
        }

        return $this;
    }

    /**
     * Puts back the Builder in a freshly created state.
     */
    public function reset(): self
    {
        $this->scheme = null;
        $this->username = null;
        $this->password = null;
        $this->host = null;
        $this->port = null;
        $this->path = null;
        $this->query = null;
        $this->fragment = null;

        return $this;
    }

    /**
     * Executes the given callback with the current instance
     * and returns the current instance.
     *
     * @param callable(self): self $callback
     */
    public function transform(callable $callback): static
    {
        return $callback($this);
    }

    public function when(callable|bool $condition, callable $onSuccess, ?callable $onFail = null): static
    {
        if (!is_bool($condition)) {
            $condition = $condition($this);
        }

        return match (true) {
            $condition => $onSuccess($this),
            null !== $onFail => $onFail($this),
            default => $this,
        } ?? $this;
    }

    /**
     * @throws SyntaxError if the URI can not be build with the current Builder state
     */
    public function guard(Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUri = null): self
    {
        try {
            $this->build($baseUri);

            return $this;
        } catch (Throwable $exception) {
            throw new SyntaxError('The current builder cannot generate a valid URI.', previous: $exception);
        }
    }

    /**
     * Tells whether the URI can be built with the current Builder state.
     */
    public function validate(Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUri = null): bool
    {
        try {
            $this->build($baseUri);

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function build(Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUri = null): Uri
    {
        $authority = $this->buildAuthority();
        $path = $this->buildPath($authority);
        $uriString = UriString::buildUri(
            $this->scheme,
            $authority,
            $path,
            Encoder::encodeQueryOrFragment($this->query),
            Encoder::encodeQueryOrFragment($this->fragment)
        );

        return Uri::new(null === $baseUri ? $uriString : UriString::resolve($uriString, match (true) {
            $baseUri instanceof Rfc3986Uri => $baseUri->toString(),
            $baseUri instanceof WhatWgUrl => $baseUri->toAsciiString(),
            default => $baseUri,
        }));
    }

    /**
     * @throws SyntaxError
     */
    private function buildAuthority(): ?string
    {
        if (null === $this->host) {
            (null === $this->username && null === $this->password && null === $this->port)
            || throw new SyntaxError('The User Information and/or the Port component(s) are set without a Host component being present.');

            return null;
        }

        $authority = $this->host;
        if (null !== $this->username || null !== $this->password) {
            $userInfo = Encoder::encodeUser($this->username);
            if (null !== $this->password) {
                $userInfo .= ':'.Encoder::encodePassword($this->password);
            }

            $authority = $userInfo.'@'.$authority;
        }

        if (null !== $this->port) {
            return $authority.':'.$this->port;
        }

        return $authority;
    }

    /**
     * @throws SyntaxError
     */
    private function buildPath(?string $authority): ?string
    {
        if (null === $this->path || '' === $this->path) {
            return $this->path;
        }

        $path = Encoder::encodePath($this->path);
        if (null !== $authority) {
            return str_starts_with($path, '/') ? $path : '/'.$path;
        }

        if (str_starts_with($path, '//')) {
            return '/.'.$path;
        }

        $colonPos = strpos($path, ':');
        if (false !== $colonPos && null === $this->scheme) {
            $slashPos = strpos($path, '/');
            (false !== $slashPos && $colonPos > $slashPos) || throw new SyntaxError('In absence of the scheme and authority components, the first path segment cannot contain a colon (":") character.');
        }

        return $path;
    }

    /**
     * Filter a string.
     *
     * @throws SyntaxError if the submitted data cannot be converted to string
     */
    private function filterString(BackedEnum|Stringable|string|null $str): ?string
    {
        $str = match (true) {
            $str instanceof FragmentDirective => $str->toFragmentValue(),
            $str instanceof UriComponentInterface => $str->value(),
            $str instanceof BackedEnum => (string) $str->value,
            null === $str => null,
            default => (string) $str,
        };

        if (null === $str) {
            return null;
        }

        $str = str_replace(' ', '%20', $str);

        return UriString::containsRfc3987Chars($str)
            ? $str
            : throw new SyntaxError('The component value `'.$str.'` contains invalid characters.');
    }
}
