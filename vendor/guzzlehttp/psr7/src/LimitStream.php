<?php

declare(strict_types=1);

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Decorator used to return only a subset of a stream.
 */
final class LimitStream implements StreamInterface
{
    use StreamDecoratorTrait;

    /** @var int Offset to start reading from */
    private $offset;

    /** @var int Limit the number of bytes that can be read */
    private $limit;

    /** @var StreamInterface */
    private $stream;

    /**
     * @param StreamInterface $stream Stream to wrap
     * @param int             $limit  Total number of bytes to allow to be read
     *                                from the stream. Pass -1 for no limit.
     * @param int             $offset Position to seek to before reading (only
     *                                works on seekable streams).
     */
    public function __construct(
        StreamInterface $stream,
        int $limit = -1,
        int $offset = 0
    ) {
        $this->stream = $stream;
        $this->setLimit($limit);
        $this->setOffset($offset);
    }

    public function eof(): bool
    {
        // Always return true if the underlying stream is EOF
        if ($this->stream->eof()) {
            return true;
        }

        // No limit and the underlying stream is not at EOF
        if ($this->limit === -1) {
            return false;
        }

        return $this->stream->tell() >= $this->offset + $this->limit;
    }

    /**
     * Returns the size of the limited subset of data
     */
    public function getSize(): ?int
    {
        if (null === ($length = $this->stream->getSize())) {
            return null;
        }

        $size = $length - $this->offset;

        if ($this->limit !== -1) {
            $size = min($this->limit, $size);
        }

        return max(0, $size);
    }

    /**
     * Allow for a bounded seek on the read limited stream
     */
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

        if ($whence !== SEEK_SET || $offset < 0) {
            throw new \RuntimeException(sprintf(
                'Cannot seek to offset %s with whence %s',
                $offset,
                $whence
            ));
        }

        $offset += $this->offset;

        if ($this->limit !== -1) {
            if ($offset > $this->offset + $this->limit) {
                $offset = $this->offset + $this->limit;
            }
        }

        $this->stream->seek($offset);
    }

    /**
     * Give a relative tell()
     */
    public function tell(): int
    {
        return $this->stream->tell() - $this->offset;
    }

    /**
     * Set the offset to start limiting from
     *
     * @param int $offset Offset to seek to and begin byte limiting from
     *
     * @throws \RuntimeException if the stream cannot be seeked.
     */
    public function setOffset(int $offset): void
    {
        $current = $this->stream->tell();

        if ($current !== $offset) {
            // If the stream cannot seek to the offset position, then read to it
            if ($this->stream->isSeekable()) {
                $this->stream->seek($offset);
            } elseif ($current > $offset) {
                throw new \RuntimeException("Could not seek to stream offset $offset");
            } else {
                $this->stream->read($offset - $current);
            }
        }

        $this->offset = $offset;
    }

    /**
     * Set the limit of bytes that the decorator allows to be read from the
     * stream.
     *
     * @param int $limit Number of bytes to allow to be read from the stream.
     *                   Use -1 for no limit.
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
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

        if ($this->limit === -1) {
            return $this->stream->read($length);
        }

        // Check if the current position is less than the total allowed
        // bytes + original offset
        $remaining = ($this->offset + $this->limit) - $this->stream->tell();
        if ($remaining > 0) {
            // Only return the amount of requested data, ensuring that the byte
            // limit is not exceeded
            return $this->stream->read(min($remaining, $length));
        }

        return '';
    }
}
