<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation;

use Psr\Clock\ClockInterface;
use Symfony\Component\HttpFoundation\Exception\ExpiredSignedUriException;
use Symfony\Component\HttpFoundation\Exception\LogicException;
use Symfony\Component\HttpFoundation\Exception\SignedUriException;
use Symfony\Component\HttpFoundation\Exception\UnsignedUriException;
use Symfony\Component\HttpFoundation\Exception\UnverifiedSignedUriException;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class UriSigner
{
    private const STATUS_VALID = 1;
    private const STATUS_INVALID = 2;
    private const STATUS_MISSING = 3;
    private const STATUS_EXPIRED = 4;

    /**
     * @param string $hashParameter       Query string parameter to use
     * @param string $expirationParameter Query string parameter to use for expiration
     */
    public function __construct(
        #[\SensitiveParameter] private string $secret,
        private string $hashParameter = '_hash',
        private string $expirationParameter = '_expiration',
        private ?ClockInterface $clock = null,
    ) {
        if (!$secret) {
            throw new \InvalidArgumentException('A non-empty secret is required.');
        }
    }

    /**
     * Signs a URI.
     *
     * The given URI is signed by adding the query string parameter
     * which value depends on the URI and the secret.
     *
     * @param \DateTimeInterface|\DateInterval|int|null $expiration The expiration for the given URI.
     *                                                              If $expiration is a \DateTimeInterface, it's expected to be the exact date + time.
     *                                                              If $expiration is a \DateInterval, the interval is added to "now" to get the date + time.
     *                                                              If $expiration is an int, it's expected to be a timestamp in seconds of the exact date + time.
     *                                                              If $expiration is null, no expiration.
     *
     * The expiration is added as a query string parameter.
     */
    public function sign(string $uri/* , \DateTimeInterface|\DateInterval|int|null $expiration = null */): string
    {
        $expiration = null;

        if (1 < \func_num_args()) {
            $expiration = func_get_arg(1);
        }

        if (null !== $expiration && !$expiration instanceof \DateTimeInterface && !$expiration instanceof \DateInterval && !\is_int($expiration)) {
            throw new \TypeError(\sprintf('The second argument of "%s()" must be an instance of "%s" or "%s", an integer or null (%s given).', __METHOD__, \DateTimeInterface::class, \DateInterval::class, get_debug_type($expiration)));
        }

        $url = parse_url($uri);
        $params = [];

        if (isset($url['query'])) {
            parse_str($url['query'], $params);
        }

        if (isset($params[$this->hashParameter])) {
            throw new LogicException(\sprintf('URI query parameter conflict: parameter name "%s" is reserved.', $this->hashParameter));
        }

        if (isset($params[$this->expirationParameter])) {
            throw new LogicException(\sprintf('URI query parameter conflict: parameter name "%s" is reserved.', $this->expirationParameter));
        }

        if (null !== $expiration) {
            $params[$this->expirationParameter] = $this->getExpirationTime($expiration);
        }

        $uri = $this->buildUrl($url, $params);
        $params[$this->hashParameter] = $this->computeHash($uri);

        return $this->buildUrl($url, $params);
    }

    /**
     * Checks that a URI contains the correct hash.
     * Also checks if the URI has not expired (If you used expiration during signing).
     */
    public function check(string $uri): bool
    {
        return self::STATUS_VALID === $this->doVerify($uri);
    }

    public function checkRequest(Request $request): bool
    {
        return self::STATUS_VALID === $this->doVerify(self::normalize($request));
    }

    /**
     * Verify a Request or string URI.
     *
     * @throws UnsignedUriException         If the URI is not signed
     * @throws UnverifiedSignedUriException If the signature is invalid
     * @throws ExpiredSignedUriException    If the URI has expired
     * @throws SignedUriException
     */
    public function verify(Request|string $uri): void
    {
        $uri = self::normalize($uri);
        $status = $this->doVerify($uri);

        match ($status) {
            self::STATUS_VALID => null,
            self::STATUS_INVALID => throw new UnverifiedSignedUriException(),
            self::STATUS_EXPIRED => throw new ExpiredSignedUriException(),
            default => throw new UnsignedUriException(),
        };
    }

    private function computeHash(string $uri): string
    {
        return strtr(rtrim(base64_encode(hash_hmac('sha256', $uri, $this->secret, true)), '='), ['/' => '_', '+' => '-']);
    }

    private function buildUrl(array $url, array $params = []): string
    {
        ksort($params, \SORT_STRING);
        $url['query'] = http_build_query($params, '', '&');

        $scheme = isset($url['scheme']) ? $url['scheme'].'://' : '';
        $host = $url['host'] ?? '';
        $port = isset($url['port']) ? ':'.$url['port'] : '';
        $user = $url['user'] ?? '';
        $pass = isset($url['pass']) ? ':'.$url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = $url['path'] ?? '';
        $query = $url['query'] ? '?'.$url['query'] : '';
        $fragment = isset($url['fragment']) ? '#'.$url['fragment'] : '';

        return $scheme.$user.$pass.$host.$port.$path.$query.$fragment;
    }

    private function getExpirationTime(\DateTimeInterface|\DateInterval|int $expiration): string
    {
        if ($expiration instanceof \DateTimeInterface) {
            return $expiration->format('U');
        }

        if ($expiration instanceof \DateInterval) {
            return $this->now()->add($expiration)->format('U');
        }

        return (string) $expiration;
    }

    private function now(): \DateTimeImmutable
    {
        return $this->clock?->now() ?? \DateTimeImmutable::createFromFormat('U', time());
    }

    /**
     * @return self::STATUS_*
     */
    private function doVerify(string $uri): int
    {
        $url = parse_url($uri);
        $params = [];

        if (isset($url['query'])) {
            parse_str($url['query'], $params);
        }

        if (empty($params[$this->hashParameter])) {
            return self::STATUS_MISSING;
        }

        $hash = $params[$this->hashParameter];
        unset($params[$this->hashParameter]);

        if (!hash_equals($this->computeHash($this->buildUrl($url, $params)), strtr(rtrim($hash, '='), ['/' => '_', '+' => '-']))) {
            return self::STATUS_INVALID;
        }

        if (!$expiration = $params[$this->expirationParameter] ?? false) {
            return self::STATUS_VALID;
        }

        if ($this->now()->getTimestamp() < $expiration) {
            return self::STATUS_VALID;
        }

        return self::STATUS_EXPIRED;
    }

    private static function normalize(Request|string $uri): string
    {
        if ($uri instanceof Request) {
            $qs = ($qs = $uri->server->get('QUERY_STRING')) ? '?'.$qs : '';
            $uri = $uri->getSchemeAndHttpHost().$uri->getBaseUrl().$uri->getPathInfo().$qs;
        }

        return $uri;
    }
}
