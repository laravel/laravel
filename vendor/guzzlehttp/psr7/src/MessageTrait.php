<?php

declare(strict_types=1);

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Trait implementing functionality common to requests and responses.
 */
trait MessageTrait
{
    /** @var string[][] Map of all registered headers, as original name => array of values */
    private $headers = [];

    /** @var string[] Map of lowercase header name => original name at registration */
    private $headerNames = [];

    /** @var string */
    private $protocol = '1.1';

    /** @var StreamInterface|null */
    private $stream;

    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    /**
     * @return static
     */
    public function withProtocolVersion($version): MessageInterface
    {
        if (!\is_string($version)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing %s to MessageInterface::withProtocolVersion() is deprecated; guzzlehttp/psr7 3.0 requires string.',
                \get_debug_type($version)
            );
        }

        $this->assertProtocolVersion($version);

        if ($this->protocol === $version) {
            return $this;
        }

        $new = clone $this;
        $new->protocol = $version;

        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader($header): bool
    {
        return isset($this->headerNames[Utils::asciiToLower($header)]);
    }

    public function getHeader($header): array
    {
        $header = Utils::asciiToLower($header);

        if (!isset($this->headerNames[$header])) {
            return [];
        }

        $header = $this->headerNames[$header];

        return $this->headers[$header];
    }

    public function getHeaderLine($header): string
    {
        return implode(', ', $this->getHeader($header));
    }

    /**
     * @return static
     */
    public function withHeader($header, $value): MessageInterface
    {
        $this->assertHeader($header);
        $values = \is_array($value) ? $value : [$value];
        foreach ($values as $item) {
            if (!\is_string($item) && (\is_scalar($item) || $item === null)) {
                \trigger_deprecation(
                    'guzzlehttp/psr7',
                    '2.11',
                    'Passing %s to MessageInterface::withHeader() is deprecated; guzzlehttp/psr7 3.0 requires string|string[].',
                    \get_debug_type($item)
                );

                break;
            }
        }
        $value = $this->normalizeHeaderValue($value);
        $normalized = Utils::asciiToLower($header);

        $new = clone $this;
        if (isset($new->headerNames[$normalized])) {
            unset($new->headers[$new->headerNames[$normalized]]);
        }
        $new->headerNames[$normalized] = $header;
        $new->headers[$header] = $value;

        return $new;
    }

    /**
     * @return static
     */
    public function withAddedHeader($header, $value): MessageInterface
    {
        $this->assertHeader($header);
        $values = \is_array($value) ? $value : [$value];
        foreach ($values as $item) {
            if (!\is_string($item) && (\is_scalar($item) || $item === null)) {
                \trigger_deprecation(
                    'guzzlehttp/psr7',
                    '2.11',
                    'Passing %s to MessageInterface::withAddedHeader() is deprecated; guzzlehttp/psr7 3.0 requires string|string[].',
                    \get_debug_type($item)
                );

                break;
            }
        }
        $value = $this->normalizeHeaderValue($value);
        $normalized = Utils::asciiToLower($header);

        $new = clone $this;
        if (isset($new->headerNames[$normalized])) {
            $header = $this->headerNames[$normalized];
            $new->headers[$header] = array_merge($this->headers[$header], $value);
        } else {
            $new->headerNames[$normalized] = $header;
            $new->headers[$header] = $value;
        }

        return $new;
    }

    /**
     * @return static
     */
    public function withoutHeader($header): MessageInterface
    {
        $normalized = Utils::asciiToLower($header);

        if (!isset($this->headerNames[$normalized])) {
            return $this;
        }

        $header = $this->headerNames[$normalized];

        $new = clone $this;
        unset($new->headers[$header], $new->headerNames[$normalized]);

        return $new;
    }

    public function getBody(): StreamInterface
    {
        if (!$this->stream) {
            $this->stream = Utils::streamFor('');
        }

        return $this->stream;
    }

    /**
     * @return static
     */
    public function withBody(StreamInterface $body): MessageInterface
    {
        if ($body === $this->stream) {
            return $this;
        }

        $new = clone $this;
        $new->stream = $body;

        return $new;
    }

    /**
     * @param (string|string[])[] $headers
     */
    private function setHeaders(array $headers): void
    {
        $this->headerNames = $this->headers = [];
        foreach ($headers as $header => $value) {
            // Numeric array keys are converted to int by PHP.
            $header = (string) $header;

            $this->assertHeader($header);
            $values = \is_array($value) ? $value : [$value];
            foreach ($values as $item) {
                if (!\is_string($item) && (\is_scalar($item) || $item === null)) {
                    \trigger_deprecation(
                        'guzzlehttp/psr7',
                        '2.11',
                        'Passing %s to %s::__construct() is deprecated; guzzlehttp/psr7 3.0 requires string|string[].',
                        \get_debug_type($item),
                        static::class
                    );

                    break;
                }
            }
            $value = $this->normalizeHeaderValue($value);
            $normalized = Utils::asciiToLower($header);
            if (isset($this->headerNames[$normalized])) {
                $header = $this->headerNames[$normalized];
                $this->headers[$header] = array_merge($this->headers[$header], $value);
            } else {
                $this->headerNames[$normalized] = $header;
                $this->headers[$header] = $value;
            }
        }
    }

    /**
     * @param mixed $value
     *
     * @return string[]
     */
    private function normalizeHeaderValue($value): array
    {
        if (is_array($value) && $value === []) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing an empty array as a header value is deprecated; guzzlehttp/psr7 3.0 rejects empty header value arrays.'
            );
        }

        if (!is_array($value)) {
            return $this->trimAndValidateHeaderValues([$value]);
        }

        return $this->trimAndValidateHeaderValues($value);
    }

    /**
     * Trims whitespace from the header values.
     *
     * Spaces and tabs ought to be excluded by parsers when extracting the field value from a header field.
     *
     * header-field = field-name ":" OWS field-value OWS
     * OWS          = *( SP / HTAB )
     *
     * @param mixed[] $values Header values
     *
     * @return string[] Trimmed header values
     *
     * @see https://datatracker.ietf.org/doc/html/rfc7230#section-3.2.4
     */
    private function trimAndValidateHeaderValues(array $values): array
    {
        return array_map(function ($value) {
            if (!is_scalar($value) && null !== $value) {
                throw new \InvalidArgumentException(sprintf(
                    'Header value must be scalar or null but %s provided.',
                    is_object($value) ? get_class($value) : gettype($value)
                ));
            }

            // Convert non-finite floats explicitly, as implicit coercion of
            // NAN emits a warning on PHP 8.5.
            if (is_float($value) && !is_finite($value)) {
                $value = is_nan($value) ? 'NAN' : ($value > 0 ? 'INF' : '-INF');
            }

            $trimmed = trim((string) $value, " \t");
            $this->assertValue($trimmed);

            return $trimmed;
        }, array_values($values));
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc7230#section-3.2
     *
     * @param mixed $header
     */
    private function assertHeader($header): void
    {
        if (!is_string($header)) {
            throw new \InvalidArgumentException(sprintf(
                'Header name must be a string but %s provided.',
                is_object($header) ? get_class($header) : gettype($header)
            ));
        }

        if (!preg_match('/^[a-zA-Z0-9\'`#$%&*+.^_|~!-]+$/D', $header)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" is not valid header name.', $header)
            );
        }
    }

    /**
     * @param mixed $version
     */
    private function assertProtocolVersion($version): void
    {
        if (is_string($version)) {
            $this->assertNoLineSeparators($version, 'Protocol version');
        }
    }

    private function assertNoLineSeparators(string $value, string $field): void
    {
        if (strpbrk($value, "\r\n") !== false) {
            throw new \InvalidArgumentException($field.' must not contain CR or LF characters.');
        }
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc7230#section-3.2
     *
     * field-value    = *( field-content / obs-fold )
     * field-content  = field-vchar [ 1*( SP / HTAB ) field-vchar ]
     * field-vchar    = VCHAR / obs-text
     * VCHAR          = %x21-7E
     * obs-text       = %x80-FF
     * obs-fold       = CRLF 1*( SP / HTAB )
     */
    private function assertValue(string $value): void
    {
        // The regular expression intentionally does not support the obs-fold production, because as
        // per RFC 7230#3.2.4:
        //
        // A sender MUST NOT generate a message that includes
        // line folding (i.e., that has any field-value that contains a match to
        // the obs-fold rule) unless the message is intended for packaging
        // within the message/http media type.
        //
        // Clients must not send a request with line folding and a server sending folded headers is
        // likely very rare. Line folding is a fairly obscure feature of HTTP/1.1 and thus not accepting
        // folding is not likely to break any legitimate use case.
        if (!preg_match('/^[\x20\x09\x21-\x7E\x80-\xFF]*$/D', $value)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" is not valid header value.', $value)
            );
        }
    }
}
