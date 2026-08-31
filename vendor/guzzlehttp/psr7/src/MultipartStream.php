<?php

declare(strict_types=1);

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Stream that when read returns bytes for a streaming multipart or
 * multipart/form-data stream.
 */
final class MultipartStream implements StreamInterface
{
    use StreamDecoratorTrait;

    /** @var string */
    private $boundary;

    /** @var StreamInterface */
    private $stream;

    private const BOUNDARY_CHARS = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'()+_,-./:=? ";

    /**
     * @param array       $elements Array of associative arrays, each containing a
     *                              required "name" key mapping to the form field,
     *                              name, a required "contents" key mapping to any
     *                              non-array value accepted by Utils::streamFor()
     *                              (non-string scalar field values are cast to
     *                              string), or an array for nested expansion.
     *                              Optional keys include "headers" (associative
     *                              array of custom headers) and "filename" (string
     *                              to send as the filename in the part).
     *                              When "contents" is an array, it is recursively
     *                              expanded into multiple fields using bracket notation
     *                              (e.g., name[0][key]). Empty arrays produce no fields.
     *                              The "filename" and "headers" options cannot be used
     *                              with array contents.
     * @param string|null $boundary You can optionally provide a specific boundary
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $elements = [], ?string $boundary = null)
    {
        if ($boundary !== null && !self::isValidBoundary($boundary)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing an invalid multipart boundary to MultipartStream::__construct() is deprecated; guzzlehttp/psr7 3.0 rejects invalid multipart boundaries.'
            );
        }

        $this->boundary = $boundary ?: bin2hex(random_bytes(20));
        $this->stream = $this->createStream($elements);
    }

    public function getBoundary(): string
    {
        return $this->boundary;
    }

    public function isWritable(): bool
    {
        return false;
    }

    /**
     * Get the headers needed before transferring the content of a POST file
     *
     * @param array<array-key, string> $headers
     */
    private function getHeaders(array $headers): string
    {
        $str = '';
        foreach ($headers as $key => $value) {
            $key = (string) $key;
            $str .= "{$key}: {$value}\r\n";
        }

        return "--{$this->boundary}\r\n".trim($str, " \n\r\t\0\x0B")."\r\n\r\n";
    }

    /**
     * Create the aggregate stream that will be used to upload the POST data
     */
    protected function createStream(array $elements = []): StreamInterface
    {
        $stream = new AppendStream();

        foreach ($elements as $element) {
            if (!is_array($element)) {
                throw new \UnexpectedValueException('An array is expected');
            }
            $this->addElement($stream, $element);
        }

        // Add the trailing boundary with CRLF
        $stream->addStream(Utils::streamFor("--{$this->boundary}--\r\n"));

        return $stream;
    }

    private function addElement(AppendStream $stream, array $element): void
    {
        foreach (['contents', 'name'] as $key) {
            if (!array_key_exists($key, $element)) {
                throw new \InvalidArgumentException("A '{$key}' key is required");
            }
        }

        if (!is_string($element['name']) && !is_int($element['name'])) {
            throw new \InvalidArgumentException("The 'name' key must be a string or integer");
        }

        if (is_array($element['contents'])) {
            if (array_key_exists('filename', $element) || array_key_exists('headers', $element)) {
                throw new \InvalidArgumentException(
                    "The 'filename' and 'headers' options cannot be used when 'contents' is an array"
                );
            }

            $this->addNestedElements($stream, $element['contents'], (string) $element['name']);

            return;
        }

        $contents = $element['contents'];
        if (is_scalar($contents) && !is_string($contents)) {
            // Multipart field values are byte strings on the wire, so finite
            // numeric and boolean field values are cast to string here rather
            // than tripping streamFor()'s non-string-scalar deprecation. Non-finite
            // floats are deprecated and normalized here too, so the deprecation is
            // reported against MultipartStream instead of transitively through
            // streamFor().
            if (is_float($contents) && !is_finite($contents)) {
                \trigger_deprecation(
                    'guzzlehttp/psr7',
                    '2.12',
                    'Passing a non-finite float as multipart contents is deprecated; guzzlehttp/psr7 3.0 rejects non-finite floats.'
                );

                $contents = is_nan($contents) ? 'NAN' : ($contents > 0 ? 'INF' : '-INF');
            }

            $contents = (string) $contents;
        }
        $element['contents'] = Utils::streamFor($contents);

        if (empty($element['filename'])) {
            $uri = $element['contents']->getMetadata('uri');
            if ($uri && \is_string($uri) && \substr($uri, 0, 6) !== 'php://' && \substr($uri, 0, 7) !== 'data://') {
                $element['filename'] = $uri;
            }
        }

        [$body, $headers] = $this->createElement(
            (string) $element['name'],
            $element['contents'],
            $element['filename'] ?? null,
            $element['headers'] ?? []
        );

        $stream->addStream(Utils::streamFor($this->getHeaders($headers)));
        $stream->addStream($body);
        $stream->addStream(Utils::streamFor("\r\n"));
    }

    /**
     * Recursively expand array contents into multiple form fields.
     *
     * @param array<array-key, mixed> $contents
     */
    private function addNestedElements(AppendStream $stream, array $contents, string $root): void
    {
        foreach ($contents as $key => $value) {
            $fieldName = $root === '' ? sprintf('[%s]', (string) $key) : sprintf('%s[%s]', $root, (string) $key);

            if (is_array($value)) {
                $this->addNestedElements($stream, $value, $fieldName);
            } else {
                $this->addElement($stream, ['name' => $fieldName, 'contents' => $value]);
            }
        }
    }

    /**
     * @param array<array-key, mixed> $headers
     *
     * @return array{0: StreamInterface, 1: array<array-key, string>}
     */
    private function createElement(string $name, StreamInterface $stream, ?string $filename, array $headers): array
    {
        $headers = self::normalizePartHeaders($headers);

        // Set a default content-disposition header if one was no provided
        $disposition = self::getHeader($headers, 'content-disposition');
        if (!$disposition) {
            $headers['Content-Disposition'] = ($filename === '0' || $filename)
                ? sprintf(
                    'form-data; name="%s"; filename="%s"',
                    $name,
                    basename($filename)
                )
                : "form-data; name=\"{$name}\"";
        }

        // Set a default content-length header if one was no provided
        $length = self::getHeader($headers, 'content-length');
        if (!$length) {
            if ($length = $stream->getSize()) {
                $headers['Content-Length'] = (string) $length;
            }
        }

        // Set a default Content-Type if one was not supplied
        $type = self::getHeader($headers, 'content-type');
        if (!$type && ($filename === '0' || $filename)) {
            $headers['Content-Type'] = MimeType::fromFilename($filename) ?? 'application/octet-stream';
        }

        return [$stream, $headers];
    }

    /**
     * @param array<array-key, string> $headers
     */
    private static function getHeader(array $headers, string $key): ?string
    {
        $lowercaseHeader = Utils::asciiToLower($key);
        foreach ($headers as $k => $v) {
            if (Utils::asciiToLower((string) $k) === $lowercaseHeader) {
                return $v;
            }
        }

        return null;
    }

    private static function isValidBoundary(string $boundary): bool
    {
        $length = strlen($boundary);

        if ($length < 1 || $length > 70 || $boundary[$length - 1] === ' ') {
            return false;
        }

        return strspn($boundary, self::BOUNDARY_CHARS) === $length;
    }

    /**
     * @param array<array-key, mixed> $headers
     *
     * @return array<array-key, string>
     */
    private static function normalizePartHeaders(array $headers): array
    {
        $normalized = [];

        foreach ($headers as $key => $value) {
            self::deprecateInvalidPartHeaderName((string) $key);

            if (!is_string($value)) {
                if (!is_scalar($value) && $value !== null && !(is_object($value) && method_exists($value, '__toString'))) {
                    throw new \InvalidArgumentException(sprintf(
                        'Multipart part header value must be a string or stringable value but %s provided.',
                        \get_debug_type($value)
                    ));
                }

                \trigger_deprecation(
                    'guzzlehttp/psr7',
                    '2.11',
                    'Passing %s as a multipart part header value is deprecated; guzzlehttp/psr7 3.0 requires string multipart part header values.',
                    \get_debug_type($value)
                );
            }

            $value = (string) $value;

            self::deprecateInvalidPartHeaderValue($value);

            $normalized[$key] = $value;
        }

        return $normalized;
    }

    private static function deprecateInvalidPartHeaderName(string $name): void
    {
        if (!preg_match('/^[a-zA-Z0-9\'`#$%&*+.^_|~!-]+$/D', $name)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing an invalid multipart part header name to MultipartStream is deprecated; guzzlehttp/psr7 3.0 rejects invalid multipart part header names.'
            );
        }
    }

    private static function deprecateInvalidPartHeaderValue(string $value): void
    {
        if (!preg_match('/^[\x20\x09\x21-\x7E\x80-\xFF]*$/D', $value)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing an invalid multipart part header value to MultipartStream is deprecated; guzzlehttp/psr7 3.0 rejects invalid multipart part header values.'
            );
        }
    }
}
