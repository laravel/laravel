<?php

declare(strict_types=1);

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Stream decorator trait
 *
 * @property StreamInterface $stream
 */
trait StreamDecoratorTrait
{
    /**
     * @param StreamInterface $stream Stream to decorate
     */
    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    /**
     * Magic method used to create a new stream if streams are not added in
     * the constructor of a decorator (e.g., LazyOpenStream).
     *
     * @return StreamInterface
     */
    public function __get(string $name)
    {
        if ($name === 'stream') {
            $this->stream = $this->createStream();

            return $this->stream;
        }

        throw new \UnexpectedValueException("$name not found on class");
    }

    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->seek(0);
            }

            return $this->getContents();
        } catch (\Throwable $e) {
            if (\PHP_VERSION_ID >= 70400) {
                throw $e;
            }
            trigger_error(sprintf('%s::__toString exception: %s', self::class, (string) $e), E_USER_ERROR);

            return '';
        }
    }

    public function getContents(): string
    {
        return Utils::copyToString($this);
    }

    /**
     * Allow decorators to implement custom methods
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        /** @var callable $callable */
        $callable = [$this->stream, $method];
        $result = ($callable)(...$args);

        // Always return the wrapped object if the result is a return $this
        return $result === $this->stream ? $this : $result;
    }

    public function close(): void
    {
        $this->stream->close();
    }

    /**
     * @return mixed
     */
    public function getMetadata($key = null)
    {
        if ($key !== null && !\is_string($key)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing %s to StreamInterface::getMetadata() is deprecated; guzzlehttp/psr7 3.0 requires string|null for $key.',
                \get_debug_type($key)
            );
        }

        return $this->stream->getMetadata($key);
    }

    public function detach()
    {
        return $this->stream->detach();
    }

    public function getSize(): ?int
    {
        return $this->stream->getSize();
    }

    public function eof(): bool
    {
        return $this->stream->eof();
    }

    public function tell(): int
    {
        return $this->stream->tell();
    }

    public function isReadable(): bool
    {
        return $this->stream->isReadable();
    }

    public function isWritable(): bool
    {
        return $this->stream->isWritable();
    }

    public function isSeekable(): bool
    {
        return $this->stream->isSeekable();
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        if (!\is_int($offset)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing %s to StreamInterface::seek() is deprecated; guzzlehttp/psr7 3.0 requires int for $offset.',
                \get_debug_type($offset)
            );
        }

        if (!\is_int($whence)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing %s to StreamInterface::seek() is deprecated; guzzlehttp/psr7 3.0 requires int for $whence.',
                \get_debug_type($whence)
            );
        }

        $this->stream->seek($offset, $whence);
    }

    public function read($length): string
    {
        if (!\is_int($length)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing %s to StreamInterface::read() is deprecated; guzzlehttp/psr7 3.0 requires int for $length.',
                \get_debug_type($length)
            );
        }

        return $this->stream->read($length);
    }

    public function write($string): int
    {
        if (!\is_string($string)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing %s to StreamInterface::write() is deprecated; guzzlehttp/psr7 3.0 requires string for $string.',
                \get_debug_type($string)
            );
        }

        return $this->stream->write($string);
    }

    /**
     * Implement in subclasses to dynamically create streams when requested.
     *
     * @throws \BadMethodCallException
     */
    protected function createStream(): StreamInterface
    {
        throw new \BadMethodCallException('Not implemented');
    }
}
