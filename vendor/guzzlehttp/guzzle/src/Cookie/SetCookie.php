<?php

namespace GuzzleHttp\Cookie;

use GuzzleHttp\Psr7;

/**
 * Set-Cookie object
 */
class SetCookie
{
    /**
     * @var array
     */
    private static $defaults = [
        'Name' => null,
        'Value' => null,
        'Domain' => null,
        'Path' => '/',
        'Max-Age' => null,
        'Expires' => null,
        'Secure' => false,
        'Discard' => false,
        'HttpOnly' => false,
    ];

    /**
     * @var array Cookie data
     */
    private $data;

    /**
     * @var bool Whether this cookie was set without a Domain attribute
     */
    private $hostOnly = false;

    /**
     * Create a new SetCookie object from a string.
     *
     * @param string $cookie Set-Cookie header string
     */
    public static function fromString(string $cookie): self
    {
        // Create the default return array
        $data = self::$defaults;
        // Explode the cookie string using a series of semicolons
        $pieces = \array_filter(\array_map(static function (string $piece): string {
            return \trim($piece, " \n\r\t\0\x0B");
        }, \explode(';', $cookie)));
        // The name of the cookie (first kvp) must exist and include an equal sign.
        if (!isset($pieces[0]) || \strpos($pieces[0], '=') === false) {
            return new self($data);
        }

        // Add the cookie pieces into the parsed data array
        foreach ($pieces as $part) {
            $cookieParts = \explode('=', $part, 2);
            $key = \trim($cookieParts[0], " \n\r\t\0\x0B");
            $value = isset($cookieParts[1])
                ? \trim($cookieParts[1], " \n\r\t\0\x0B")
                : true;

            // Only check for non-cookies when cookies have been found
            if (!isset($data['Name'])) {
                $data['Name'] = $key;
                $data['Value'] = $value;
            } else {
                foreach (\array_keys(self::$defaults) as $search) {
                    if (Psr7\Utils::caselessEquals($search, $key)) {
                        if ($search === 'Max-Age') {
                            if (is_numeric($value)) {
                                $data[$search] = (int) $value;
                            }
                        } elseif ($search === 'Secure' || $search === 'Discard' || $search === 'HttpOnly') {
                            if ($value) {
                                $data[$search] = true;
                            }
                        } else {
                            $data[$search] = $value;
                        }
                        continue 2;
                    }
                }
                if (Psr7\Utils::caselessEquals('HostOnly', $key)) {
                    continue;
                }
                $data[$key] = $value;
            }
        }

        return new self($data);
    }

    /**
     * @param array $data Array of cookie data provided by a Cookie parser
     */
    public function __construct(array $data = [])
    {
        $this->data = self::$defaults;

        if (\array_key_exists('HostOnly', $data)) {
            if (!\is_bool($data['HostOnly'])) {
                throw new \InvalidArgumentException('Cookie field "HostOnly" must be a boolean');
            }
            $this->setHostOnly($data['HostOnly']);
            unset($data['HostOnly']);
        }

        if (isset($data['Name'])) {
            $this->setName($data['Name']);
        }

        if (isset($data['Value'])) {
            $this->setValue($data['Value']);
        }

        if (isset($data['Domain'])) {
            $this->setDomain($data['Domain']);
        }

        if (isset($data['Path'])) {
            $this->setPath($data['Path']);
        }

        if (isset($data['Max-Age'])) {
            $this->setMaxAge($data['Max-Age']);
        }

        if (isset($data['Expires'])) {
            $this->setExpires($data['Expires']);
        }

        if (isset($data['Secure'])) {
            $this->setSecure($data['Secure']);
        }

        if (isset($data['Discard'])) {
            $this->setDiscard($data['Discard']);
        }

        if (isset($data['HttpOnly'])) {
            $this->setHttpOnly($data['HttpOnly']);
        }

        // Set the remaining values that don't have extra validation logic
        foreach (array_diff(array_keys($data), array_keys(self::$defaults)) as $key) {
            $this->data[$key] = $data[$key];
        }

        // Extract the Expires value and turn it into a UNIX timestamp if needed
        $maxAge = $this->getMaxAge();
        if (!$this->getExpires() && $maxAge !== null) {
            // Calculate the Expires date
            $this->setExpires(self::maxAgeToExpires($maxAge, \time()));
        } elseif (null !== ($expires = $this->getExpires()) && !\is_numeric($expires)) {
            $this->setExpires($expires);
        }
    }

    private static function maxAgeToExpires(int $maxAge, int $now): int
    {
        if ($maxAge <= 0) {
            return $now - 1;
        }
        if ($maxAge > \PHP_INT_MAX - $now) {
            return \PHP_INT_MAX;
        }

        return $now + $maxAge;
    }

    public function __toString()
    {
        $str = $this->data['Name'].'='.($this->data['Value'] ?? '').'; ';
        foreach ($this->data as $k => $v) {
            if ($k === 'Domain' && $this->getHostOnly()) {
                continue;
            }
            if ($k !== 'Name' && $k !== 'Value' && $v !== null && $v !== false) {
                if ($k === 'Expires') {
                    $str .= 'Expires='.\gmdate('D, d M Y H:i:s \G\M\T', $v).'; ';
                } else {
                    $str .= ($v === true ? $k : "{$k}={$v}").'; ';
                }
            }
        }

        return \rtrim($str, '; ');
    }

    public function toArray(): array
    {
        $data = $this->data;
        if ($this->getHostOnly()) {
            $data['HostOnly'] = true;
        }

        return $data;
    }

    /**
     * Get the cookie name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->data['Name'];
    }

    /**
     * Set the cookie name.
     *
     * @param string $name Cookie name
     */
    public function setName($name): void
    {
        if (!is_string($name)) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a string to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }

        $this->data['Name'] = (string) $name;
    }

    /**
     * Get the cookie value.
     *
     * @return string|null
     */
    public function getValue()
    {
        return $this->data['Value'];
    }

    /**
     * Set the cookie value.
     *
     * @param string $value Cookie value
     */
    public function setValue($value): void
    {
        if (!is_string($value)) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a string to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }

        $this->data['Value'] = (string) $value;
    }

    /**
     * Get the domain.
     *
     * @return string|null
     */
    public function getDomain()
    {
        return $this->data['Domain'];
    }

    /**
     * Set the domain of the cookie.
     *
     * @param string|null $domain
     */
    public function setDomain($domain): void
    {
        if (!is_string($domain) && null !== $domain) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a string or null to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }

        $this->data['Domain'] = null === $domain ? null : (string) $domain;
    }

    /**
     * Get whether this cookie is scoped to the origin host only.
     *
     * @return bool
     */
    public function getHostOnly()
    {
        return $this->hostOnly;
    }

    /**
     * Set whether this cookie is scoped to the origin host only.
     *
     * @param bool $hostOnly Set to true for host-only cookies
     */
    public function setHostOnly(bool $hostOnly): void
    {
        $this->hostOnly = $hostOnly;
    }

    /**
     * Get the path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->data['Path'];
    }

    /**
     * Set the path of the cookie.
     *
     * @param string $path Path of the cookie
     */
    public function setPath($path): void
    {
        if (!is_string($path)) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a string to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }

        $this->data['Path'] = (string) $path;
    }

    /**
     * Maximum lifetime of the cookie in seconds.
     *
     * @return int|null
     */
    public function getMaxAge()
    {
        return null === $this->data['Max-Age'] ? null : (int) $this->data['Max-Age'];
    }

    /**
     * Set the max-age of the cookie.
     *
     * @param int|null $maxAge Max age of the cookie in seconds
     */
    public function setMaxAge($maxAge): void
    {
        if (!is_int($maxAge) && null !== $maxAge) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing an int or null to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }

        $this->data['Max-Age'] = $maxAge === null ? null : (int) $maxAge;
    }

    /**
     * The UNIX timestamp when the cookie Expires.
     *
     * @return string|int|null
     */
    public function getExpires()
    {
        return $this->data['Expires'];
    }

    /**
     * Set the unix timestamp for which the cookie will expire.
     *
     * @param int|string|null $timestamp Unix timestamp or any English textual datetime description.
     */
    public function setExpires($timestamp): void
    {
        if (!is_int($timestamp) && !is_string($timestamp) && null !== $timestamp) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing an int, string or null to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }

        if (null === $timestamp) {
            $this->data['Expires'] = null;
        } elseif (\is_numeric($timestamp)) {
            $this->data['Expires'] = (int) $timestamp;
        } else {
            // Store unparseable dates as session cookies, not as expired cookies.
            $expires = \strtotime((string) $timestamp);
            $this->data['Expires'] = $expires === false ? null : $expires;
        }
    }

    /**
     * Get whether or not this is a secure cookie.
     *
     * @return bool
     */
    public function getSecure()
    {
        return $this->data['Secure'];
    }

    /**
     * Set whether or not the cookie is secure.
     *
     * @param bool $secure Set to true or false if secure
     */
    public function setSecure($secure): void
    {
        if (!is_bool($secure)) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a bool to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }

        $this->data['Secure'] = (bool) $secure;
    }

    /**
     * Get whether or not this is a session cookie.
     *
     * @return bool|null
     */
    public function getDiscard()
    {
        return $this->data['Discard'];
    }

    /**
     * Set whether or not this is a session cookie.
     *
     * @param bool $discard Set to true or false if this is a session cookie
     */
    public function setDiscard($discard): void
    {
        if (!is_bool($discard)) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a bool to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }

        $this->data['Discard'] = (bool) $discard;
    }

    /**
     * Get whether or not this is an HTTP only cookie.
     *
     * @return bool
     */
    public function getHttpOnly()
    {
        return $this->data['HttpOnly'];
    }

    /**
     * Set whether or not this is an HTTP only cookie.
     *
     * @param bool $httpOnly Set to true or false if this is HTTP only
     */
    public function setHttpOnly($httpOnly): void
    {
        if (!is_bool($httpOnly)) {
            \trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a bool to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }

        $this->data['HttpOnly'] = (bool) $httpOnly;
    }

    /**
     * Check if the cookie matches a path value.
     *
     * A request-path path-matches a given cookie-path if at least one of
     * the following conditions holds:
     *
     * - The cookie-path and the request-path are identical.
     * - The cookie-path is a prefix of the request-path, and the last
     *   character of the cookie-path is %x2F ("/").
     * - The cookie-path is a prefix of the request-path, and the first
     *   character of the request-path that is not included in the cookie-
     *   path is a %x2F ("/") character.
     *
     * @param string $requestPath Path to check against
     */
    public function matchesPath(string $requestPath): bool
    {
        $cookiePath = $this->getPath();

        // Match on exact matches or when path is the default empty "/"
        if ($cookiePath === '/' || $cookiePath === $requestPath) {
            return true;
        }

        // Ensure that the cookie-path is a prefix of the request path.
        if (0 !== \strpos($requestPath, $cookiePath)) {
            return false;
        }

        // Match if the last character of the cookie-path is "/"
        if (\substr($cookiePath, -1, 1) === '/') {
            return true;
        }

        // Match if the first character not included in cookie path is "/"
        return \substr($requestPath, \strlen($cookiePath), 1) === '/';
    }

    /**
     * Check if the cookie matches a domain value.
     *
     * @param string $domain Domain to check against
     */
    public function matchesDomain(string $domain): bool
    {
        $cookieDomain = $this->getDomain();
        if (null === $cookieDomain) {
            return !$this->getHostOnly();
        }

        if ($this->getHostOnly()) {
            return Psr7\Utils::asciiToLower($domain) === Psr7\Utils::asciiToLower($cookieDomain);
        }

        // Remove the leading '.' as per spec in RFC 6265.
        // https://datatracker.ietf.org/doc/html/rfc6265#section-5.2.3
        $cookieDomain = Psr7\Utils::asciiToLower($cookieDomain);
        if ($cookieDomain !== '' && $cookieDomain[0] === '.') {
            /** @var string */
            $cookieDomain = \substr($cookieDomain, 1);
        }
        if ('' === $cookieDomain) {
            return false;
        }

        $domain = Psr7\Utils::asciiToLower($domain);
        if ($domain === $cookieDomain) {
            return true;
        }

        // IP literals and numeric hosts are exact-match-only per RFC 6265.
        // Only the exact match above may succeed for those cookie domains.
        if (self::isIpAddressOrNumericHost($cookieDomain)) {
            return false;
        }

        // Matching the subdomain according to RFC 6265.
        // https://datatracker.ietf.org/doc/html/rfc6265#section-5.1.3
        if (\filter_var($domain, \FILTER_VALIDATE_IP)) {
            return false;
        }

        return (bool) \preg_match('/\.'.\preg_quote($cookieDomain, '/').'$/D', $domain);
    }

    private static function isIpAddressOrNumericHost(string $host): bool
    {
        // Strip one root dot before detection so trailing-dot numeric hosts
        // still cannot be matched by subdomains.
        if ($host !== '' && \str_ends_with($host, '.')) {
            $host = \substr($host, 0, -1);
        }

        if (\str_starts_with($host, '[') && \str_ends_with($host, ']')) {
            $host = \substr($host, 1, -1);
        }

        if (\filter_var($host, \FILTER_VALIDATE_IP) !== false) {
            return true;
        }

        // Public DNS names do not have an all-numeric rightmost label; treat
        // those private/internal hosts as exact-match-only too.
        $labels = \explode('.', $host);
        $last = (string) \end($labels);

        return $last !== '' && \ctype_digit($last);
    }

    /**
     * Check if the cookie is expired.
     */
    public function isExpired(): bool
    {
        return $this->getExpires() !== null && \time() > $this->getExpires();
    }

    /**
     * Check if the cookie is valid according to RFC 6265.
     *
     * @return bool|string Returns true if valid or an error message if invalid
     */
    public function validate()
    {
        $name = $this->getName();
        if ($name === '') {
            return 'The cookie name must not be empty';
        }

        // Check if any of the invalid characters are present in the cookie name
        if (\preg_match('/[\x00-\x20\x22\x28-\x29\x2c\x2f\x3a-\x40\x5c\x7b\x7d\x7f]/', $name) !== 0) {
            return 'Cookie name must not contain invalid characters: ASCII '
                .'Control characters (0-31;127), space, tab and the '
                .'following characters: ()<>@,;:\"/?={}';
        }

        // Value must not be null. 0 and empty string are valid. Empty strings
        // are technically against RFC 6265, but known to happen in the wild.
        $value = $this->getValue();
        if ($value === null) {
            return 'The cookie value must not be empty';
        }

        // Domains must not be empty, but may be omitted. "0" is not a valid
        // internet domain, but may be used as server name in a private network.
        $domain = $this->getDomain();
        if ($domain === '' || (null !== $domain && '' === \ltrim(\trim($domain, " \n\r\t\0\x0B"), '.'))) {
            return 'The cookie domain must not be empty';
        }

        return true;
    }
}
