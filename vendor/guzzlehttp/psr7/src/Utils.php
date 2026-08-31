<?php

declare(strict_types=1);

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

final class Utils
{
    /**
     * Converts ASCII uppercase letters in a string to lowercase.
     *
     * Unlike strtolower(), which honors LC_CTYPE before PHP 8.2, the
     * conversion is locale-independent and leaves every non-ASCII byte
     * unchanged, as HTTP protocol elements require.
     */
    public static function asciiToLower(string $string): string
    {
        return strtr($string, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
    }

    /**
     * Converts ASCII lowercase letters in a string to uppercase.
     *
     * Unlike strtoupper(), which honors LC_CTYPE before PHP 8.2, the
     * conversion is locale-independent and leaves every non-ASCII byte
     * unchanged, as HTTP protocol elements require.
     */
    public static function asciiToUpper(string $string): string
    {
        return strtr($string, 'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }

    /**
     * Converts the first character of a string to uppercase when it is an
     * ASCII lowercase letter.
     *
     * Unlike ucfirst(), which honors LC_CTYPE before PHP 8.2, the conversion
     * is locale-independent and leaves every non-ASCII byte unchanged, as
     * HTTP protocol elements require.
     */
    public static function asciiUcFirst(string $string): string
    {
        if ($string === '') {
            return '';
        }

        return self::asciiToUpper($string[0]).substr($string, 1);
    }

    /**
     * Checks whether the haystack contains the needle, comparing ASCII
     * letters case-insensitively and without locale sensitivity.
     */
    public static function caselessContains(string $haystack, string $needle): bool
    {
        return str_contains(self::asciiToLower($haystack), self::asciiToLower($needle));
    }

    /**
     * Checks whether two strings are equal, comparing ASCII letters
     * case-insensitively and without locale sensitivity.
     */
    public static function caselessEquals(string $left, string $right): bool
    {
        return self::asciiToLower($left) === self::asciiToLower($right);
    }

    /**
     * Remove the items given by the keys, case insensitively from the data.
     *
     * @param (string|int)[] $keys
     */
    public static function caselessRemove(array $keys, array $data): array
    {
        $result = [];

        foreach ($keys as &$key) {
            $key = self::asciiToLower((string) $key);
        }

        foreach ($data as $k => $v) {
            if (!in_array(self::asciiToLower((string) $k), $keys)) {
                $result[$k] = $v;
            }
        }

        return $result;
    }

    /**
     * Copy the contents of a stream into another stream until the given number
     * of bytes have been read.
     *
     * The copy stops if the destination write returns 0, for example a
     * BufferStream at its high water mark or a full DroppingStream. For a
     * guaranteed full copy use a normal writable stream such as a file or
     * php://temp stream.
     *
     * @param StreamInterface $source Stream to read from
     * @param StreamInterface $dest   Stream to write to
     * @param int             $maxLen Maximum number of bytes to read. Pass -1
     *                                to read the entire stream.
     *
     * @throws \RuntimeException on error.
     */
    public static function copyToStream(StreamInterface $source, StreamInterface $dest, int $maxLen = -1): void
    {
        $bufferSize = 8192;

        if ($maxLen === -1) {
            while (!$source->eof()) {
                $buf = $source->read($bufferSize);
                if ($buf === '') {
                    break;
                }

                if (!self::writeAll($dest, $buf)) {
                    break;
                }
            }
        } else {
            $remaining = $maxLen;
            while ($remaining > 0 && !$source->eof()) {
                $buf = $source->read(min($bufferSize, $remaining));
                $len = strlen($buf);
                if (!$len) {
                    break;
                }
                $remaining -= $len;
                if (!self::writeAll($dest, $buf)) {
                    break;
                }
            }
        }
    }

    /**
     * Writes the full buffer to the destination, retrying short writes.
     *
     * Returns false when the destination write returns 0 or less.
     */
    private static function writeAll(StreamInterface $dest, string $buf): bool
    {
        $written = 0;
        $len = strlen($buf);

        while ($written < $len) {
            $result = $dest->write(substr($buf, $written));
            if ($result <= 0) {
                return false;
            }

            $written += $result;
        }

        return true;
    }

    /**
     * Copy the contents of a stream into a string until the given number of
     * bytes have been read.
     *
     * @param StreamInterface $stream Stream to read
     * @param int             $maxLen Maximum number of bytes to read. Pass -1
     *                                to read the entire stream.
     *
     * @throws \RuntimeException on error.
     */
    public static function copyToString(StreamInterface $stream, int $maxLen = -1): string
    {
        $buffer = '';

        if ($maxLen === -1) {
            while (!$stream->eof()) {
                $buf = $stream->read(1048576);
                if ($buf === '') {
                    break;
                }
                $buffer .= $buf;
            }

            return $buffer;
        }

        $len = 0;
        while (!$stream->eof() && $len < $maxLen) {
            $buf = $stream->read($maxLen - $len);
            if ($buf === '') {
                break;
            }
            $buffer .= $buf;
            $len = strlen($buffer);
        }

        return $buffer;
    }

    /**
     * Calculate a hash of a stream.
     *
     * This method reads the entire stream to calculate a rolling hash, based
     * on PHP's `hash_init` functions.
     *
     * @param StreamInterface $stream    Stream to calculate the hash for
     * @param string          $algo      Hash algorithm (e.g. md5, crc32, etc)
     * @param bool            $rawOutput Whether or not to use raw output
     *
     * @throws \RuntimeException on error.
     */
    public static function hash(StreamInterface $stream, string $algo, bool $rawOutput = false): string
    {
        $pos = $stream->tell();

        if ($pos > 0) {
            $stream->rewind();
        }

        $ctx = hash_init($algo);
        while (!$stream->eof()) {
            hash_update($ctx, $stream->read(1048576));
        }

        $out = hash_final($ctx, $rawOutput);
        $stream->seek($pos);

        return $out;
    }

    /**
     * Clone and modify a request with the given changes.
     *
     * This method is useful for reducing the number of clones needed to mutate
     * a message.
     *
     * The changes can be one of:
     * - method: (string) Changes the HTTP method.
     * - set_headers: (array) Sets the given headers. Values must be strings
     *   or non-empty arrays of strings.
     * - remove_headers: (array) Remove the given headers. Values may be
     *   strings or integers.
     * - body: (mixed) Sets the given body. Present non-null values are converted
     *   with self::streamFor(), including scalar values, resources, streams,
     *   iterators, callable arrays, closures, invokable objects, and objects
     *   with __toString(). String inputs remain literal bodies.
     * - uri: (UriInterface) Set the URI.
     * - query: (string) Set the query string value of the URI.
     * - version: (string) Set the protocol version.
     *
     * @param RequestInterface $request Request to clone and modify.
     * @param array            $changes Changes to apply.
     */
    public static function modifyRequest(RequestInterface $request, array $changes): RequestInterface
    {
        if (!$changes) {
            return $request;
        }

        self::warnOnInvalidModifyRequestChanges($changes);

        $headers = $request->getHeaders();

        if (!isset($changes['uri'])) {
            $uri = $request->getUri();
        } else {
            // Remove the host header if one is on the URI
            $host = $changes['uri']->getHost();
            if ($host !== '') {
                if (isset($changes['set_headers']) && is_array($changes['set_headers'])) {
                    foreach (array_keys($changes['set_headers']) as $header) {
                        if (self::asciiToLower((string) $header) === 'host') {
                            throw new \InvalidArgumentException(
                                'Cannot modify request with both a URI containing a host and an explicit Host header.'
                            );
                        }
                    }
                }

                $changes['set_headers']['Host'] = $host;

                if ($port = $changes['uri']->getPort()) {
                    $standardPorts = ['http' => 80, 'https' => 443];
                    $scheme = $changes['uri']->getScheme();
                    if (isset($standardPorts[$scheme]) && $port != $standardPorts[$scheme]) {
                        $changes['set_headers']['Host'] .= ':'.$port;
                    }
                }
            }
            $uri = $changes['uri'];
        }

        if (!empty($changes['remove_headers'])) {
            $headers = self::caselessRemove($changes['remove_headers'], $headers);
        }

        if (!empty($changes['set_headers'])) {
            $headers = self::caselessRemove(array_keys($changes['set_headers']), $headers);
            $headers = $changes['set_headers'] + $headers;
        }

        if (isset($changes['query'])) {
            $uri = $uri->withQuery($changes['query']);
        }

        $hasHost = false;
        foreach (array_keys($headers) as $header) {
            if (self::asciiToLower((string) $header) === 'host') {
                $hasHost = true;
                break;
            }
        }

        // Match Request::__construct() by adding a Host header when one is not provided.
        if (!$hasHost && $uri->getHost() !== '') {
            $host = $uri->getHost();

            if (($port = $uri->getPort()) !== null) {
                $host .= ':'.$port;
            }

            $headers = ['Host' => [$host]] + $headers;
        }

        $new = $request;

        if (isset($changes['method'])) {
            $new = $new->withMethod($changes['method']);
        }

        if (isset($changes['uri']) || isset($changes['query'])) {
            $new = $new->withUri($uri, true);
        }

        if ($headers !== $new->getHeaders()) {
            foreach (array_keys($new->getHeaders()) as $header) {
                /** @var RequestInterface */
                $new = $new->withoutHeader((string) $header);
            }

            $addedHeaders = [];
            foreach ($headers as $header => $value) {
                $header = (string) $header;
                $normalized = self::asciiToLower($header);

                if (isset($addedHeaders[$normalized])) {
                    /** @var RequestInterface */
                    $new = $new->withAddedHeader($addedHeaders[$normalized], $value);
                } else {
                    /** @var RequestInterface */
                    $new = $new->withHeader($header, $value);
                    $addedHeaders[$normalized] = $header;
                }
            }
        }

        if (isset($changes['body'])) {
            /** @var RequestInterface */
            $new = $new->withBody(self::streamFor($changes['body']));
        }

        if (isset($changes['version'])) {
            /** @var RequestInterface */
            $new = $new->withProtocolVersion($changes['version']);
        }

        return $new;
    }

    /**
     * @param array<array-key, mixed> $changes
     */
    private static function warnOnInvalidModifyRequestChanges(array $changes): void
    {
        foreach (['method', 'query', 'version'] as $key) {
            if (\array_key_exists($key, $changes) && !\is_string($changes[$key])) {
                self::warnOnInvalidModifyRequestChange($key, 'string', $changes[$key]);
            }
        }

        if (\array_key_exists('uri', $changes) && !$changes['uri'] instanceof UriInterface) {
            self::warnOnInvalidModifyRequestChange('uri', 'UriInterface', $changes['uri']);
        }

        if (\array_key_exists('body', $changes) && $changes['body'] === null) {
            self::warnOnInvalidModifyRequestChange('body', 'resource|string|int|float|bool|StreamInterface|callable|\Iterator|\Stringable', $changes['body']);
        }

        if (\array_key_exists('set_headers', $changes)) {
            if (!\is_array($changes['set_headers'])) {
                self::warnOnInvalidModifyRequestChange('set_headers', 'array<array-key, string|non-empty-array<array-key, string>>', $changes['set_headers']);
            } else {
                foreach ($changes['set_headers'] as $header => $value) {
                    $headerPath = \sprintf('set_headers.%s', (string) $header);

                    if (\is_array($value)) {
                        if ($value === []) {
                            self::warnOnInvalidModifyRequestChange($headerPath, 'string|non-empty-array<array-key, string>', $value);

                            break;
                        }

                        foreach ($value as $index => $item) {
                            if (!\is_string($item)) {
                                self::warnOnInvalidModifyRequestChange(\sprintf('%s.%s', $headerPath, (string) $index), 'string', $item);

                                break 2;
                            }
                        }
                    } elseif (!\is_string($value)) {
                        self::warnOnInvalidModifyRequestChange($headerPath, 'string|non-empty-array<array-key, string>', $value);

                        break;
                    }
                }
            }
        }

        if (!\array_key_exists('remove_headers', $changes)) {
            return;
        }

        if (!\is_array($changes['remove_headers'])) {
            self::warnOnInvalidModifyRequestChange('remove_headers', 'array<array-key, string|int>', $changes['remove_headers']);

            return;
        }

        foreach ($changes['remove_headers'] as $index => $header) {
            if (!\is_string($header) && !\is_int($header)) {
                self::warnOnInvalidModifyRequestChange(\sprintf('remove_headers.%s', (string) $index), 'string|int', $header);

                return;
            }
        }
    }

    /**
     * @param mixed $value
     */
    private static function warnOnInvalidModifyRequestChange(string $key, string $expected, $value): void
    {
        \trigger_deprecation(
            'guzzlehttp/psr7',
            '2.11',
            'Passing %s to Utils::modifyRequest() change "%s" is deprecated; guzzlehttp/psr7 3.0 requires %s.',
            \get_debug_type($value),
            $key,
            $expected
        );
    }

    /**
     * Read a line from the stream up to the maximum allowed buffer length.
     *
     * @param StreamInterface $stream    Stream to read from
     * @param int|null        $maxLength Maximum buffer length
     */
    public static function readLine(StreamInterface $stream, ?int $maxLength = null): string
    {
        $buffer = '';
        $size = 0;

        while (!$stream->eof()) {
            if ('' === ($byte = $stream->read(1))) {
                return $buffer;
            }
            $buffer .= $byte;
            // Break when a new line is found or the max length - 1 is reached
            if ($byte === "\n" || ++$size === $maxLength - 1) {
                break;
            }
        }

        return $buffer;
    }

    /**
     * Redact the password in the user info part of a URI.
     */
    public static function redactUserInfo(UriInterface $uri): UriInterface
    {
        $userInfo = $uri->getUserInfo();

        if (false !== ($pos = \strpos($userInfo, ':'))) {
            return $uri->withUserInfo(\substr($userInfo, 0, $pos), '***');
        }

        return $uri;
    }

    /**
     * Create a new stream based on the input type.
     *
     * Options is an associative array that can contain the following keys:
     * - metadata: Array of custom metadata.
     * - size: Size of the stream.
     *
     * This method accepts the following `$resource` types:
     * - `Psr\Http\Message\StreamInterface`: Returns the value as-is.
     * - `string`: Creates a stream object that uses the given string as the contents.
     * - `resource`: Creates a stream object that wraps the given PHP stream resource.
     * - `Iterator`: If the provided value implements `Iterator`, then a read-only
     *   stream object will be created that wraps the given iterable. Each time the
     *   stream is read from, data from the iterator will fill a buffer and will be
     *   continuously called until the buffer is equal to the requested read size.
     *   Subsequent read calls will first read from the buffer and then call `next`
     *   on the underlying iterator until it is exhausted.
     * - `object` with `__toString()`: If the object has the `__toString()` method,
     *   the object will be cast to a string and then a stream will be returned that
     *   uses the string value.
     * - `NULL`: When `null` is passed, an empty stream object is returned.
     * - `callable`: When a callable array, closure, or invokable object is passed
     *   and no earlier resource or object rule applies, a read-only stream object
     *   will be created that invokes the given callable. The callable is invoked
     *   with the suggested number of bytes to read. The callable can return fewer
     *   or more bytes than requested, but MUST return `false` or `null` when there
     *   is no more data to return. Any additional bytes will be buffered and used
     *   in subsequent reads. String inputs are always treated as string bodies,
     *   even when they name callable functions.
     *
     * Passing a non-string scalar (`int`, `float`, or `bool`) is deprecated; cast
     * it to a string instead. guzzlehttp/psr7 3.0 will reject non-string scalars.
     *
     * @param resource|string|int|float|bool|StreamInterface|callable|\Iterator|null $resource Entity body data
     * @param array{size?: int, metadata?: array}                                    $options  Additional options
     *
     * @throws \InvalidArgumentException if the $resource arg is not valid.
     */
    public static function streamFor($resource = '', array $options = []): StreamInterface
    {
        if (is_scalar($resource)) {
            if (!is_string($resource)) {
                \trigger_deprecation(
                    'guzzlehttp/psr7',
                    '2.12',
                    'Passing %s to Utils::streamFor() is deprecated; cast it to a string. guzzlehttp/psr7 3.0 will only accept string, resource, StreamInterface, Stringable, Iterator, callable, or null.',
                    \gettype($resource)
                );

                if (is_float($resource) && !is_finite($resource)) {
                    // Normalized only to avoid PHP 8.5's (string) NAN warning
                    // while deprecated; 3.0 rejects non-finite floats with every
                    // other non-string scalar.
                    $resource = is_nan($resource) ? 'NAN' : ($resource > 0 ? 'INF' : '-INF');
                }
            }

            $stream = self::tryFopen('php://temp', 'r+');
            if ($resource !== '') {
                fwrite($stream, (string) $resource);
                fseek($stream, 0);
            }

            return new Stream($stream, $options);
        }

        switch (gettype($resource)) {
            case 'resource':
                /*
                 * The 'php://input' is a special stream with quirks and inconsistencies.
                 * We avoid using that stream by reading it into php://temp
                 */

                /** @var resource $resource */
                if ((\stream_get_meta_data($resource)['uri'] ?? '') === 'php://input') {
                    $stream = self::tryFopen('php://temp', 'w+');
                    stream_copy_to_stream($resource, $stream);
                    fseek($stream, 0);
                    $resource = $stream;
                }

                return new Stream($resource, $options);
            case 'object':
                /** @var object $resource */
                if ($resource instanceof StreamInterface) {
                    return $resource;
                } elseif ($resource instanceof \Iterator) {
                    return new PumpStream(function () use ($resource) {
                        if (!$resource->valid()) {
                            return false;
                        }
                        $result = $resource->current();
                        $resource->next();

                        return $result;
                    }, $options);
                } elseif (method_exists($resource, '__toString')) {
                    return self::streamFor((string) $resource, $options);
                }
                break;
            case 'NULL':
                return new Stream(self::tryFopen('php://temp', 'r+'), $options);
        }

        if (is_callable($resource)) {
            return new PumpStream($resource, $options);
        }

        throw new \InvalidArgumentException('Invalid resource type: '.gettype($resource));
    }

    /**
     * Safely opens a PHP stream resource using a filename.
     *
     * When fopen fails, PHP normally raises a warning. This function adds an
     * error handler that checks for errors and throws an exception instead.
     *
     * @param string $filename File to open
     * @param string $mode     Mode used to open the file
     *
     * @return resource
     *
     * @throws \RuntimeException if the file cannot be opened
     */
    public static function tryFopen(string $filename, string $mode)
    {
        $ex = null;
        set_error_handler(static function (int $errno, string $errstr) use ($filename, $mode, &$ex): bool {
            $ex = new \RuntimeException(sprintf(
                'Unable to open "%s" using mode "%s": %s',
                $filename,
                $mode,
                $errstr
            ));

            return true;
        });

        try {
            /** @var resource $handle */
            $handle = fopen($filename, $mode);
        } catch (\Throwable $e) {
            $ex = new \RuntimeException(sprintf(
                'Unable to open "%s" using mode "%s": %s',
                $filename,
                $mode,
                $e->getMessage()
            ), 0, $e);
        }

        restore_error_handler();

        if ($ex) {
            /** @var \RuntimeException $ex */
            throw $ex;
        }

        return $handle;
    }

    /**
     * Safely gets the contents of a given stream.
     *
     * When stream_get_contents fails, PHP normally raises a warning. This
     * function adds an error handler that checks for errors and throws an
     * exception instead.
     *
     * @param resource $stream
     *
     * @throws \RuntimeException if the stream cannot be read
     */
    public static function tryGetContents($stream): string
    {
        $ex = null;
        set_error_handler(static function (int $errno, string $errstr) use (&$ex): bool {
            $ex = new \RuntimeException(sprintf(
                'Unable to read stream contents: %s',
                $errstr
            ));

            return true;
        });

        try {
            /** @var string|false $contents */
            $contents = stream_get_contents($stream);

            if ($contents === false) {
                $ex = new \RuntimeException('Unable to read stream contents');
            }
        } catch (\Throwable $e) {
            $ex = new \RuntimeException(sprintf(
                'Unable to read stream contents: %s',
                $e->getMessage()
            ), 0, $e);
        }

        restore_error_handler();

        if ($ex) {
            /** @var \RuntimeException $ex */
            throw $ex;
        }

        return $contents;
    }

    /**
     * Returns a UriInterface for the given value.
     *
     * This function accepts a string or UriInterface and returns a
     * UriInterface for the given value. If the value is already a
     * UriInterface, it is returned as-is.
     *
     * @param string|UriInterface $uri
     *
     * @throws \InvalidArgumentException
     */
    public static function uriFor($uri): UriInterface
    {
        if ($uri instanceof UriInterface) {
            return $uri;
        }

        if (is_string($uri)) {
            return new Uri($uri);
        }

        throw new \InvalidArgumentException('URI must be a string or UriInterface');
    }
}
