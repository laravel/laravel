<?php

declare(strict_types=1);

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Stream decorator that can cache previously read bytes from a sequentially
 * read stream.
 */
final class CachingStream implements StreamInterface
{
    use StreamDecoratorTrait;

    /** @var StreamInterface Stream being wrapped */
    private $remoteStream;

    /** @var int Number of bytes to skip reading due to a write on the buffer */
    private $skipReadBytes = 0;

    /**
     * @var StreamInterface
     */
    private $stream;

    /** @var bool */
    private $detached = false;

    /**
     * We will treat the buffer object as the body of the stream
     *
     * @param StreamInterface $stream Stream to cache. The cursor is assumed to be at the beginning of the stream.
     * @param StreamInterface $target Optionally specify where data is cached
     */
    public function __construct(
        StreamInterface $stream,
        ?StreamInterface $target = null
    ) {
        $this->remoteStream = $stream;
        $this->stream = $target ?: new Stream(Utils::tryFopen('php://temp', 'r+'));
    }

    public function getSize(): ?int
    {
        if ($this->detached) {
            return null;
        }

        $remoteSize = $this->remoteStream->getSize();

        if (null === $remoteSize) {
            return null;
        }

        return max($this->stream->getSize(), $remoteSize);
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

        if ($whence === SEEK_SET) {
            $byte = $offset;
        } elseif ($whence === SEEK_CUR) {
            $byte = $offset + $this->tell();
        } elseif ($whence === SEEK_END) {
            $size = $this->remoteStream->getSize();
            if ($size === null) {
                $size = $this->cacheEntireStream();
            }
            $byte = $size + $offset;
        } else {
            throw new \InvalidArgumentException('Invalid whence');
        }

        $diff = $byte - $this->stream->getSize();

        if ($diff > 0) {
            // Read the remoteStream until we have read in at least the amount
            // of bytes requested, or we reach the end of the file.
            while ($diff > 0 && !$this->remoteStream->eof()) {
                $previousSize = $this->stream->getSize();
                $previousSkipReadBytes = $this->skipReadBytes;
                $data = $this->read($diff);
                $currentSize = $this->stream->getSize();

                if ($data === '' && $currentSize === $previousSize && $this->skipReadBytes === $previousSkipReadBytes) {
                    break;
                }

                $diff = $byte - $currentSize;
            }
        } else {
            // We can just do a normal seek since we've already seen this byte.
            $this->stream->seek($byte);
        }
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

        // Perform a regular read on any previously read data from the buffer
        $data = $this->stream->read($length);
        $remaining = $length - strlen($data);

        // More data was requested so read from the remote stream
        if ($remaining) {
            // If data was written to the buffer in a position that would have
            // been filled from the remote stream, then we must skip bytes on
            // the remote stream to emulate overwriting bytes from that
            // position. This mimics the behavior of other PHP stream wrappers.
            $remoteData = $this->remoteStream->read(
                $remaining + $this->skipReadBytes
            );

            if ($this->skipReadBytes) {
                $len = strlen($remoteData);
                $remoteData = substr($remoteData, $this->skipReadBytes);
                $this->skipReadBytes = max(0, $this->skipReadBytes - $len);
            }

            $data .= $remoteData;

            // A short cache write would silently corrupt later replays, so fail loudly.
            if ($this->stream->write($remoteData) !== strlen($remoteData)) {
                throw new \RuntimeException('Unable to cache the entire read from the remote stream');
            }
        }

        return $data;
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

        // When appending to the end of the currently read stream, you'll want
        // to skip bytes from being read from the remote stream to emulate
        // other stream wrappers. Basically replacing bytes of data of a fixed
        // length.
        $overflow = (strlen($string) + $this->tell()) - $this->remoteStream->tell();
        if ($overflow > 0) {
            $this->skipReadBytes += $overflow;
        }

        return $this->stream->write($string);
    }

    public function eof(): bool
    {
        return $this->stream->eof() && $this->remoteStream->eof();
    }

    public function detach()
    {
        if ($this->detached) {
            return null;
        }

        $position = $this->tell();

        $this->cacheEntireStream();
        $this->stream->seek($position);

        $resource = $this->stream->detach();
        $this->detached = true;

        return $resource;
    }

    /**
     * Close both the remote stream and buffer stream
     */
    public function close(): void
    {
        $this->remoteStream->close();
        $this->stream->close();
        $this->detached = true;
    }

    private function cacheEntireStream(): int
    {
        $target = new FnStream(['write' => 'strlen']);
        Utils::copyToStream($this, $target);

        return $this->tell();
    }
}
