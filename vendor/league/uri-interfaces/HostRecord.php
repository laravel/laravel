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
use Exception;
use JsonSerializable;
use League\Uri\Contracts\UriComponentInterface;
use League\Uri\Exceptions\SyntaxError;
use League\Uri\Idna\Converter as IdnConverter;
use Stringable;
use Throwable;

use function array_key_first;
use function count;
use function explode;
use function filter_var;
use function get_object_vars;
use function in_array;
use function inet_pton;
use function is_object;
use function preg_match;
use function rawurldecode;
use function strpos;
use function strtolower;
use function substr;

use const FILTER_FLAG_IPV4;
use const FILTER_FLAG_IPV6;
use const FILTER_VALIDATE_IP;

/**
 * @phpstan-type HostRecordSerializedShape array{0: array{host: ?string}, 1: array{}}
 */
final class HostRecord implements JsonSerializable
{
    /**
     * Maximum number of host cached.
     *
     * @var int
     */
    private const MAXIMUM_HOST_CACHED = 100;

    private const REGEXP_NON_ASCII_PATTERN = '/[^\x20-\x7f]/';

    /**
     * @see https://tools.ietf.org/html/rfc3986#section-3.2.2
     *
     * invalid characters in host regular expression
     */
    private const REGEXP_INVALID_HOST_CHARS = '/
        [:\/?#\[\]@ ]  # gen-delims characters as well as the space character
    /ix';

    /**
     * General registered name regular expression.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2.2
     * @see https://regex101.com/r/fptU8V/1
     */
    private const REGEXP_REGISTERED_NAME = '/
    (?(DEFINE)
        (?<unreserved>[a-z0-9_~\-])   # . is missing as it is used to separate labels
        (?<sub_delims>[!$&\'()*+,;=])
        (?<encoded>%[A-F0-9]{2})
        (?<reg_name>(?:(?&unreserved)|(?&sub_delims)|(?&encoded))*)
    )
        ^(?:(?&reg_name)\.)*(?&reg_name)\.?$
    /ix';

    /**
     * Domain name regular expression.
     *
     * Everything but the domain name length is validated
     *
     * @see https://tools.ietf.org/html/rfc1034#section-3.5
     * @see https://tools.ietf.org/html/rfc1123#section-2.1
     * @see https://regex101.com/r/71j6rt/1
     */
    private const REGEXP_DOMAIN_NAME = '/
    (?(DEFINE)
        (?<let_dig> [a-z0-9])                         # alpha digit
        (?<let_dig_hyp> [a-z0-9-])                    # alpha digit and hyphen
        (?<ldh_str> (?&let_dig_hyp){0,61}(?&let_dig)) # domain label end
        (?<label> (?&let_dig)((?&ldh_str))?)          # domain label
        (?<domain> (?&label)(\.(?&label)){0,126}\.?)  # domain name
    )
        ^(?&domain)$
    /ix';

    /**
     * @see https://tools.ietf.org/html/rfc3986#section-3.2.2
     *
     * IPvFuture regular expression
     */
    private const REGEXP_IP_FUTURE = '/^
        v(?<version>[A-F\d])+\.
        (?:
            (?<unreserved>[a-z\d_~\-\.])|
            (?<sub_delims>[!$&\'()*+,;=:])  # also include the : character
        )+
    $/ix';
    private const REGEXP_GEN_DELIMS = '/[:\/?#\[\]@ ]/';
    private const ADDRESS_BLOCK = "\xfe\x80";

    private ?bool $isDomainName = null;
    private ?bool $hasZoneIdentifier = null;
    private bool $asciiIsLoaded = false;
    private ?string $hostAsAscii = null;
    private bool $unicodeIsLoaded = false;
    private ?string $hostAsUnicode = null;
    private bool $isIpVersionLoaded = false;
    private ?string $ipVersion = null;
    private bool $isIpValueLoaded = false;
    private ?string $ipValue = null;

    private function __construct(
        public readonly ?string $value,
        public readonly HostType $type,
        public readonly HostFormat $format
    ) {
    }

    public function hasZoneIdentifier(): bool
    {
        return $this->hasZoneIdentifier ??= HostType::Ipv6 === $this->type && str_contains((string) $this->value, '%');
    }

    public function toAscii(): ?string
    {
        if (!$this->asciiIsLoaded) {
            $this->asciiIsLoaded = true;
            $this->hostAsAscii = (function (): ?string {
                if (HostType::RegisteredName !== $this->type || null === $this->value) {
                    return $this->value;
                }

                $formattedHost = rawurldecode($this->value);
                if ($formattedHost === $this->value) {
                    return $this->isDomainType() ? IdnConverter::toAscii($this->value)->domain() : strtolower($formattedHost);
                }

                return Encoder::normalizeHost($this->value);
            })();
        }

        return $this->hostAsAscii;
    }

    public function toUnicode(): ?string
    {
        if (!$this->unicodeIsLoaded) {
            $this->unicodeIsLoaded = true;
            $this->hostAsUnicode = $this->isDomainType() && null !== $this->value ? IdnConverter::toUnicode($this->value)->domain() : $this->value;
        }

        return $this->hostAsUnicode;
    }

    public function isDomainType(): bool
    {
        return $this->isDomainName ??= match (true) {
            HostType::RegisteredName !== $this->type, '' === $this->value => false,
            null === $this->value => true,
            default => is_object($result = IdnConverter::toAscii($this->value))
                && !$result->hasErrors()
                && self::isValidDomain($result->domain()),
        };
    }

    public function ipVersion(): ?string
    {
        if (!$this->isIpVersionLoaded) {
            $this->isIpVersionLoaded = true;
            $this->ipVersion = match (true) {
                HostType::Ipv4 === $this->type => '4',
                HostType::Ipv6 === $this->type => '6',
                1 === preg_match(self::REGEXP_IP_FUTURE, substr((string) $this->value, 1, -1), $matches) => $matches['version'],
                default => null,
            };
        }

        return $this->ipVersion;
    }

    public function ipValue(): ?string
    {
        if (!$this->isIpValueLoaded) {
            $this->isIpValueLoaded = true;
            $this->ipValue = (function (): ?string {
                if (HostType::RegisteredName === $this->type) {
                    return null;
                }

                if (HostType::Ipv4 === $this->type) {
                    return $this->value;
                }

                $ip = substr((string) $this->value, 1, -1);
                if (HostType::Ipv6 !== $this->type) {
                    return substr($ip, (int) strpos($ip, '.') + 1);
                }

                $pos = strpos($ip, '%');
                if (false === $pos) {
                    return $ip;
                }

                return substr($ip, 0, $pos).'%'.rawurldecode(substr($ip, $pos + 3));
            })();
        }

        return $this->ipValue;
    }

    public static function isValid(BackedEnum|Stringable|string|null $host): bool
    {
        try {
            HostRecord::from($host);

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public static function isIpv4(Stringable|string|null $host): bool
    {
        try {
            return HostType::Ipv4 === HostRecord::from($host)->type;
        } catch (Throwable) {
            return false;
        }
    }

    public static function isIpv6(Stringable|string|null $host): bool
    {
        try {
            return HostType::Ipv6 === HostRecord::from($host)->type;
        } catch (Throwable) {
            return false;
        }
    }

    public static function isIpvFuture(Stringable|string|null $host): bool
    {
        try {
            return HostType::IpvFuture === HostRecord::from($host)->type;
        } catch (Throwable) {
            return false;
        }
    }

    public static function isIp(Stringable|string|null $host): bool
    {
        return self::isIpv4($host)
            || self::isIpv6($host)
            || self::isIpvFuture($host);
    }

    public static function isRegisteredName(Stringable|string|null $host): bool
    {
        try {
            return HostType::RegisteredName === HostRecord::from($host)->type;
        } catch (Throwable) {
            return false;
        }
    }

    public static function isDomain(Stringable|string|null $host): bool
    {
        try {
            return HostRecord::from($host)->isDomainType();
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @throws SyntaxError
     */
    public static function from(BackedEnum|Stringable|string|null $host): self
    {
        if ($host instanceof BackedEnum) {
            $host = $host->value;
        }

        if ($host instanceof UriComponentInterface) {
            $host = $host->value();
        }

        if (null === $host) {
            return new self(
                value: null,
                type: HostType::RegisteredName,
                format: HostFormat::Ascii,
            );
        }

        $host = (string) $host;
        if ('' === $host) {
            return new self(
                value: '',
                type: HostType::RegisteredName,
                format: HostFormat::Ascii,
            );
        }

        static $inMemoryCache = [];
        if (isset($inMemoryCache[$host])) {
            return $inMemoryCache[$host];
        }

        if (self::MAXIMUM_HOST_CACHED < count($inMemoryCache)) {
            unset($inMemoryCache[array_key_first($inMemoryCache)]);
        }

        if ($host === filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $inMemoryCache[$host] = new self(
                value: $host,
                type: HostType::Ipv4,
                format: HostFormat::Ascii,
            );
        }

        if (str_starts_with($host, '[')) {
            str_ends_with($host, ']') || throw new SyntaxError('The host '.$host.' is not a valid IPv6 host.');

            $ipHost = substr($host, 1, -1);
            if (1 === preg_match(self::REGEXP_IP_FUTURE, $ipHost, $matches)) {
                return !in_array($matches['version'], ['4', '6'], true) ? ($inMemoryCache[$host] = new self(
                    value: $host,
                    type: HostType::IpvFuture,
                    format: HostFormat::Ascii,
                )) : throw new SyntaxError('The host '.$host.' is not a valid IPvFuture host.');
            }

            if (self::isValidIpv6Hostname($ipHost)) {
                return $inMemoryCache[$host] = new self(
                    value: $host,
                    type: HostType::Ipv6,
                    format: HostFormat::Ascii,
                );
            }

            throw new SyntaxError('The host '.$host.' is not a valid IPv6 host.');
        }

        $domainName = rawurldecode($host);
        $format = HostFormat::Unicode;
        if (1 !== preg_match(self::REGEXP_NON_ASCII_PATTERN, $domainName)) {
            $domainName = strtolower($domainName);
            $format = HostFormat::Ascii;
        }

        if (1 === preg_match(self::REGEXP_REGISTERED_NAME, $domainName)) {
            return $inMemoryCache[$host] = new self(
                value: $host,
                type: HostType::RegisteredName,
                format: $format,
            );
        }

        (HostFormat::Ascii !== $format && 1 !== preg_match(self::REGEXP_INVALID_HOST_CHARS, $domainName)) || throw new SyntaxError('`'.$host.'` is an invalid domain name : the host contains invalid characters.');
        IdnConverter::toAsciiOrFail($domainName);

        return $inMemoryCache[$host] = new self(
            value: $host,
            type: HostType::RegisteredName,
            format: $format,
        );
    }

    /**
     * Tells whether the registered name is a valid domain name according to RFC1123.
     *
     * @see http://man7.org/linux/man-pages/man7/hostname.7.html
     * @see https://tools.ietf.org/html/rfc1123#section-2.1
     */
    private static function isValidDomain(string $hostname): bool
    {
        $domainMaxLength = str_ends_with($hostname, '.') ? 254 : 253;

        return !isset($hostname[$domainMaxLength])
            && 1 === preg_match(self::REGEXP_DOMAIN_NAME, $hostname);
    }

    /**
     * Validates an Ipv6 as Host.
     *
     * @see http://tools.ietf.org/html/rfc6874#section-2
     * @see http://tools.ietf.org/html/rfc6874#section-4
     */
    private static function isValidIpv6Hostname(string $host): bool
    {
        [$ipv6, $scope] = explode('%', $host, 2) + [1 => null];
        if (null === $scope) {
            return (bool) filter_var($ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        }

        $scope = rawurldecode('%'.$scope);

        return 1 !== preg_match(self::REGEXP_NON_ASCII_PATTERN, $scope)
            && 1 !== preg_match(self::REGEXP_GEN_DELIMS, $scope)
            && false !== filter_var($ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
            && str_starts_with((string)inet_pton((string)$ipv6), self::ADDRESS_BLOCK);
    }

    public function jsonSerialize(): ?string
    {
        return $this->value;
    }

    /**
     * @return HostRecordSerializedShape
     */
    public function __serialize(): array
    {
        return [['host' => $this->value], []];
    }

    /**
     * @param HostRecordSerializedShape $data
     *
     * @throws Exception|SyntaxError
     */
    public function __unserialize(array $data): void
    {
        [$properties] = $data;
        $record = self::from($properties['host'] ?? throw new Exception('The `host` property is missing from the serialized object.'));
        //if the Host computed value are already cache this avoid recomputing them
        foreach (get_object_vars($record) as $prop => $value) {
            /* @phpstan-ignore-next-line */
            $this->{$prop} = $value;
        }
    }
}
