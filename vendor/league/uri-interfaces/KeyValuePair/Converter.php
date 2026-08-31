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

namespace League\Uri\KeyValuePair;

use BackedEnum;
use League\Uri\Exceptions\SyntaxError;
use League\Uri\StringCoercionMode;
use Stringable;

use function array_combine;
use function explode;
use function implode;
use function is_string;
use function preg_match;
use function str_replace;

use const PHP_QUERY_RFC1738;
use const PHP_QUERY_RFC3986;

final class Converter
{
    private const REGEXP_INVALID_CHARS = '/[\x00-\x1f\x7f]/';

    /**
     * @param non-empty-string $separator the query string separator
     * @param array<string> $fromRfc3986 contains all the RFC3986 encoded characters to be converted
     * @param array<string> $toEncoding contains all the expected encoded characters
     */
    private function __construct(
        private readonly string $separator,
        private readonly array $fromRfc3986 = [],
        private readonly array $toEncoding = [],
    ) {
        if ('' === $this->separator) {
            throw new SyntaxError('The separator character must be a non empty string.');
        }
    }

    /**
     * @param non-empty-string $separator
     */
    public static function new(string $separator): self
    {
        return new self($separator);
    }

    /**
     * @param non-empty-string $separator
     */
    public static function fromRFC3986(string $separator = '&'): self
    {
        return self::new($separator);
    }

    /**
     * @param non-empty-string $separator
     */
    public static function fromRFC1738(string $separator = '&'): self
    {
        return self::new($separator)
            ->withEncodingMap(['%20' => '+']);
    }

    /**
     * @param non-empty-string $separator
     *
     * @see https://url.spec.whatwg.org/#application/x-www-form-urlencoded
     */
    public static function fromFormData(string $separator = '&'): self
    {
        return self::new($separator)
            ->withEncodingMap(['%20' => '+', '%2A' => '*']);
    }

    public static function fromEncodingType(int $encType): self
    {
        return match ($encType) {
            PHP_QUERY_RFC3986 => self::fromRFC3986(),
            PHP_QUERY_RFC1738 => self::fromRFC1738(),
            default => throw new SyntaxError('Unknown or Unsupported encoding.'),
        };
    }

    /**
     * @return non-empty-string
     */
    public function separator(): string
    {
        return $this->separator;
    }

    /**
     * @return array<string, string>
     */
    public function encodingMap(): array
    {
        return array_combine($this->fromRfc3986, $this->toEncoding);
    }

    /**
     * @return array<non-empty-list<string|null>>
     */
    public function toPairs(BackedEnum|Stringable|string|int|float|bool|null $value): array
    {
        $value = StringCoercionMode::Native->coerce($value);
        if (null === $value) {
            return [];
        }

        $value = match (1) {
            preg_match(self::REGEXP_INVALID_CHARS, $value) => throw new SyntaxError('Invalid query string: `'.$value.'`.'),
            default => str_replace($this->toEncoding, $this->fromRfc3986, $value),
        };

        return array_map(
            fn (string $pair): array => explode('=', $pair, 2) + [1 => null],
            explode($this->separator, $value)
        );
    }

    /**
     * @param iterable<array{0:string|null, 1:BackedEnum|Stringable|string|bool|int|float|null}> $pairs
     */
    public function toValue(iterable $pairs): ?string
    {
        $filteredPairs = [];
        foreach ($pairs as $pair) {
            $filteredPairs[] = match (true) {
                !is_string($pair[0]) => throw new SyntaxError('the pair key MUST be a string;, `'.gettype($pair[0]).'` given.'),
                null === $pair[1] => StringCoercionMode::Native->coerce($pair[0]),
                default => StringCoercionMode::Native->coerce($pair[0]).'='.StringCoercionMode::Native->coerce($pair[1]),
            };
        }

        return match ([]) {
            $filteredPairs => null,
            default => str_replace($this->fromRfc3986, $this->toEncoding, implode($this->separator, $filteredPairs)),
        };
    }

    /**
     * @param non-empty-string $separator
     */
    public function withSeparator(string $separator): self
    {
        return match ($this->separator) {
            $separator => $this,
            default => new self($separator, $this->fromRfc3986, $this->toEncoding),
        };
    }

    /**
     * Sets the conversion map.
     *
     * Each key from the iterable structure represents the RFC3986 encoded characters as string,
     * while each value represents the expected output encoded characters
     */
    public function withEncodingMap(iterable $encodingMap): self
    {
        $fromRfc3986 = [];
        $toEncoding = [];
        foreach ($encodingMap as $from => $to) {
            [$fromRfc3986[], $toEncoding[]] = match (true) {
                !is_string($from) => throw new SyntaxError('The encoding output must be a string; `'.gettype($from).'` given.'),
                $to instanceof Stringable,
                is_string($to) => [$from, (string) $to],
                default => throw new SyntaxError('The encoding output must be a string; `'.gettype($to).'` given.'),
            };
        }

        return match (true) {
            $fromRfc3986 !== $this->fromRfc3986,
            $toEncoding !== $this->toEncoding => new self($this->separator, $fromRfc3986, $toEncoding),
            default => $this,
        };
    }
}
