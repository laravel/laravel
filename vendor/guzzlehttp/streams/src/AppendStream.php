<?php
namespace GuzzleHttp\Stream;

use GuzzleHttp\Stream\Exception\CannotAttachException;

/**
 * Reads from multiple streams, one after the other.
 *
 * This is a read-only stream decorator.
 */
class AppendStream implements StreamInterface
{
    /** @var StreamInterface[] Streams being decorated */
    private $streams = [];

    private $seekable = true;
    private $current = 0;
    private $pos = 0;
    private $detached = false;

    /**
     * @param StreamInterface[] $streams Streams to decorate. Each stream must
     *                                   be readable.
     */
    public function __construct(array $streams = [])
    {
        foreach ($streams as $stream) {
            $this->addStream($stream);
        }
    }

    public function __toString()
    {
        try {
            $this->seek(0);
            return $this->getContents();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Add a stream to the AppendStream
     *
     * @param StreamInterface $stream Stream to append. Must be readable.
     *
     * @throws \InvalidArgumentException if the stream is not readable
     */
    public function addStream(StreamInterface $stream)
    {
        if (!$stream->isReadable()) {
            throw new \InvalidArgumentException('Each stream must be readable');
        }

        // The stream is only seekable if all streams are seekable
        if (!$stream->isSeekable()) {
            $this->seekable = false;
        }

        $this->streams[] = $stream;
    }

    public function getContents()
    {
        return Utils::copyToString($this);
    }

    /**
     * Closes each attached stream.
     *
     * {@inheritdoc}
     */
    public function close()
    {
        $this->pos = $this->current = 0;

        foreach ($this->streams as $stream) {
            $stream->close();
        }

        $this->streams = [];
    }

    /**
     * Detaches each attached stream
     *
     * {@inheritdoc}
     */
    public function detach()
    {
        $this->close();
        $this->detached = true;
    }

    public function attach($stream)
    {
        throw new CannotAttachException();
    }

    public function tell()
    {
        return $this->pos;
    }

    /**
     * Tries to calculate the size by adding the size of each stream.
     *
     * If any of the streams do not return a valid number, then the size of the
     * append stream cannot be determined and null is returned.
     *
     * {@inheritdoc}
     */
    public function getSize()
    {
        $size = 0;

        foreach ($this->streams as $stream) {
            $s = $stream->getSize();
            if ($s === null) {
                return null;
            }
            $size += $s;
        }

        return $size;
    }

    public function eof()
    {
        return !$this->streams ||
            ($this->current >= count($this->streams) - 1 &&
             $this->streams[$this->current]->eof());
    }

    /**
     * Attempts to seek to the given position. Only supports SEEK_SET.
     *
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->seekable || $whence !== SEEK_SET) {
            return false;
        }

        $success = true;
        $this->pos = $this->current = 0;

        // Rewind each stream
        foreach ($this->streams as $stream) {
            if (!$stream->seek(0)) {
                $success = false;
            }
        }

        if (!$success) {
            return false;
        }

        // Seek to the actual position by reading from each stream
        while ($this->pos < $offset && !$this->eof()) {
            $this->read(min(8096, $offset - $this->pos));
        }

        return $this->pos == $offset;
    }

    /**
     * Reads from all of the appended streams until the length is met or EOF.
     *
     * {@inheritdoc}
     */
    public function read($length)
    {
        $buffer = '';
        $total = count($this->streams) - 1;
        $remaining = $length;

        while ($remaining > 0) {
            // Progress to the next stream if needed.
            if ($this->streams[$this->current]->eof()) {
                if ($this->current == $total) {
                    break;
                }
                $this->current++;
            }
            $buffer .= $this->streams[$this->current]->read($remaining);
            $remaining = $length - strlen($buffer);
        }

        $this->pos += strlen($buffer);

        return $buffer;
    }

    public function isReadable()
    {
        return true;
    }

    public function isWritable()
    {
        return false;
    }

    public function isSeekable()
    {
        return $this->seekable;
    }

    public function write($string)
    {
        return false;
    }

    public function getMetadata($key = null)
    {
        return $key ? null : [];
    }
}
