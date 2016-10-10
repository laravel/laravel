<?php
namespace GuzzleHttp\Stream;

use GuzzleHttp\Stream\Exception\CannotAttachException;

/**
 * Provides a buffer stream that can be written to to fill a buffer, and read
 * from to remove bytes from the buffer.
 *
 * This stream returns a "hwm" metadata value that tells upstream consumers
 * what the configured high water mark of the stream is, or the maximum
 * preferred size of the buffer.
 *
 * @package GuzzleHttp\Stream
 */
class BufferStream implements StreamInterface
{
    private $hwm;
    private $buffer = '';

    /**
     * @param int $hwm High water mark, representing the preferred maximum
     *                 buffer size. If the size of the buffer exceeds the high
     *                 water mark, then calls to write will continue to succeed
     *                 but will return false to inform writers to slow down
     *                 until the buffer has been drained by reading from it.
     */
    public function __construct($hwm = 16384)
    {
        $this->hwm = $hwm;
    }

    public function __toString()
    {
        return $this->getContents();
    }

    public function getContents()
    {
        $buffer = $this->buffer;
        $this->buffer = '';

        return $buffer;
    }

    public function close()
    {
        $this->buffer = '';
    }

    public function detach()
    {
        $this->close();
    }

    public function attach($stream)
    {
        throw new CannotAttachException();
    }

    public function getSize()
    {
        return strlen($this->buffer);
    }

    public function isReadable()
    {
        return true;
    }

    public function isWritable()
    {
        return true;
    }

    public function isSeekable()
    {
        return false;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        return false;
    }

    public function eof()
    {
        return strlen($this->buffer) === 0;
    }

    public function tell()
    {
        return false;
    }

    /**
     * Reads data from the buffer.
     */
    public function read($length)
    {
        $currentLength = strlen($this->buffer);

        if ($length >= $currentLength) {
            // No need to slice the buffer because we don't have enough data.
            $result = $this->buffer;
            $this->buffer = '';
        } else {
            // Slice up the result to provide a subset of the buffer.
            $result = substr($this->buffer, 0, $length);
            $this->buffer = substr($this->buffer, $length);
        }

        return $result;
    }

    /**
     * Writes data to the buffer.
     */
    public function write($string)
    {
        $this->buffer .= $string;

        if (strlen($this->buffer) >= $this->hwm) {
            return false;
        }

        return strlen($string);
    }

    public function getMetadata($key = null)
    {
        if ($key == 'hwm') {
            return $this->hwm;
        }

        return $key ? null : [];
    }
}
