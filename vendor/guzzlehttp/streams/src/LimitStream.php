<?php
namespace GuzzleHttp\Stream;

use GuzzleHttp\Stream\Exception\SeekException;

/**
 * Decorator used to return only a subset of a stream
 */
class LimitStream implements StreamInterface
{
    use StreamDecoratorTrait;

    /** @var int Offset to start reading from */
    private $offset;

    /** @var int Limit the number of bytes that can be read */
    private $limit;

    /**
     * @param StreamInterface $stream Stream to wrap
     * @param int             $limit  Total number of bytes to allow to be read
     *                                from the stream. Pass -1 for no limit.
     * @param int|null        $offset Position to seek to before reading (only
     *                                works on seekable streams).
     */
    public function __construct(
        StreamInterface $stream,
        $limit = -1,
        $offset = 0
    ) {
        $this->stream = $stream;
        $this->setLimit($limit);
        $this->setOffset($offset);
    }

    public function eof()
    {
        // Always return true if the underlying stream is EOF
        if ($this->stream->eof()) {
            return true;
        }

        // No limit and the underlying stream is not at EOF
        if ($this->limit == -1) {
            return false;
        }

        $tell = $this->stream->tell();
        if ($tell === false) {
            return false;
        }

        return $tell >= $this->offset + $this->limit;
    }

    /**
     * Returns the size of the limited subset of data
     * {@inheritdoc}
     */
    public function getSize()
    {
        if (null === ($length = $this->stream->getSize())) {
            return null;
        } elseif ($this->limit == -1) {
            return $length - $this->offset;
        } else {
            return min($this->limit, $length - $this->offset);
        }
    }

    /**
     * Allow for a bounded seek on the read limited stream
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if ($whence !== SEEK_SET || $offset < 0) {
            return false;
        }

        $offset += $this->offset;

        if ($this->limit !== -1) {
            if ($offset > $this->offset + $this->limit) {
                $offset = $this->offset + $this->limit;
            }
        }

        return $this->stream->seek($offset);
    }

    /**
     * Give a relative tell()
     * {@inheritdoc}
     */
    public function tell()
    {
        return $this->stream->tell() - $this->offset;
    }

    /**
     * Set the offset to start limiting from
     *
     * @param int $offset Offset to seek to and begin byte limiting from
     *
     * @return self
     * @throws SeekException
     */
    public function setOffset($offset)
    {
        $current = $this->stream->tell();

        if ($current !== $offset) {
            // If the stream cannot seek to the offset position, then read to it
            if (!$this->stream->seek($offset)) {
                if ($current > $offset) {
                    throw new SeekException($this, $offset);
                } else {
                    $this->stream->read($offset - $current);
                }
            }
        }

        $this->offset = $offset;

        return $this;
    }

    /**
     * Set the limit of bytes that the decorator allows to be read from the
     * stream.
     *
     * @param int $limit Number of bytes to allow to be read from the stream.
     *                   Use -1 for no limit.
     * @return self
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function read($length)
    {
        if ($this->limit == -1) {
            return $this->stream->read($length);
        }

        // Check if the current position is less than the total allowed
        // bytes + original offset
        $remaining = ($this->offset + $this->limit) - $this->stream->tell();
        if ($remaining > 0) {
            // Only return the amount of requested data, ensuring that the byte
            // limit is not exceeded
            return $this->stream->read(min($remaining, $length));
        } else {
            return false;
        }
    }
}
