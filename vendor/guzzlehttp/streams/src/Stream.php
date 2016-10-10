<?php
namespace GuzzleHttp\Stream;

/**
 * PHP stream implementation
 */
class Stream implements StreamInterface
{
    private $stream;
    private $size;
    private $seekable;
    private $readable;
    private $writable;
    private $uri;
    private $customMetadata;

    /** @var array Hash of readable and writable stream types */
    private static $readWriteHash = [
        'read' => [
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true,
            'c+b' => true, 'rt' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a+' => true
        ],
        'write' => [
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true,
            'c+' => true, 'wb' => true, 'w+b' => true, 'r+b' => true,
            'x+b' => true, 'c+b' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true
        ]
    ];

    /**
     * Create a new stream based on the input type.
     *
     * This factory accepts the same associative array of options as described
     * in the constructor.
     *
     * @param resource|string|StreamInterface $resource Entity body data
     * @param array                           $options  Additional options
     *
     * @return Stream
     * @throws \InvalidArgumentException if the $resource arg is not valid.
     */
    public static function factory($resource = '', array $options = [])
    {
        $type = gettype($resource);

        if ($type == 'string') {
            $stream = fopen('php://temp', 'r+');
            if ($resource !== '') {
                fwrite($stream, $resource);
                fseek($stream, 0);
            }
            return new self($stream, $options);
        }

        if ($type == 'resource') {
            return new self($resource, $options);
        }

        if ($resource instanceof StreamInterface) {
            return $resource;
        }

        if ($type == 'object' && method_exists($resource, '__toString')) {
            return self::factory((string) $resource, $options);
        }

        if (is_callable($resource)) {
            return new PumpStream($resource, $options);
        }

        if ($resource instanceof \Iterator) {
            return new PumpStream(function () use ($resource) {
                if (!$resource->valid()) {
                    return false;
                }
                $result = $resource->current();
                $resource->next();
                return $result;
            }, $options);
        }

        throw new \InvalidArgumentException('Invalid resource type: ' . $type);
    }

    /**
     * This constructor accepts an associative array of options.
     *
     * - size: (int) If a read stream would otherwise have an indeterminate
     *   size, but the size is known due to foreknownledge, then you can
     *   provide that size, in bytes.
     * - metadata: (array) Any additional metadata to return when the metadata
     *   of the stream is accessed.
     *
     * @param resource $stream  Stream resource to wrap.
     * @param array    $options Associative array of options.
     *
     * @throws \InvalidArgumentException if the stream is not a stream resource
     */
    public function __construct($stream, $options = [])
    {
        if (!is_resource($stream)) {
            throw new \InvalidArgumentException('Stream must be a resource');
        }

        if (isset($options['size'])) {
            $this->size = $options['size'];
        }

        $this->customMetadata = isset($options['metadata'])
            ? $options['metadata']
            : [];

        $this->attach($stream);
    }

    /**
     * Closes the stream when the destructed
     */
    public function __destruct()
    {
        $this->close();
    }

    public function __toString()
    {
        if (!$this->stream) {
            return '';
        }

        $this->seek(0);

        return (string) stream_get_contents($this->stream);
    }

    public function getContents()
    {
        return $this->stream ? stream_get_contents($this->stream) : '';
    }

    public function close()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }

        $this->detach();
    }

    public function detach()
    {
        $result = $this->stream;
        $this->stream = $this->size = $this->uri = null;
        $this->readable = $this->writable = $this->seekable = false;

        return $result;
    }

    public function attach($stream)
    {
        $this->stream = $stream;
        $meta = stream_get_meta_data($this->stream);
        $this->seekable = $meta['seekable'];
        $this->readable = isset(self::$readWriteHash['read'][$meta['mode']]);
        $this->writable = isset(self::$readWriteHash['write'][$meta['mode']]);
        $this->uri = $this->getMetadata('uri');
    }

    public function getSize()
    {
        if ($this->size !== null) {
            return $this->size;
        }

        if (!$this->stream) {
            return null;
        }

        // Clear the stat cache if the stream has a URI
        if ($this->uri) {
            clearstatcache(true, $this->uri);
        }

        $stats = fstat($this->stream);
        if (isset($stats['size'])) {
            $this->size = $stats['size'];
            return $this->size;
        }

        return null;
    }

    public function isReadable()
    {
        return $this->readable;
    }

    public function isWritable()
    {
        return $this->writable;
    }

    public function isSeekable()
    {
        return $this->seekable;
    }

    public function eof()
    {
        return !$this->stream || feof($this->stream);
    }

    public function tell()
    {
        return $this->stream ? ftell($this->stream) : false;
    }

    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        return $this->seekable
            ? fseek($this->stream, $offset, $whence) === 0
            : false;
    }

    public function read($length)
    {
        return $this->readable ? fread($this->stream, $length) : false;
    }

    public function write($string)
    {
        // We can't know the size after writing anything
        $this->size = null;

        return $this->writable ? fwrite($this->stream, $string) : false;
    }

    public function getMetadata($key = null)
    {
        if (!$this->stream) {
            return $key ? null : [];
        } elseif (!$key) {
            return $this->customMetadata + stream_get_meta_data($this->stream);
        } elseif (isset($this->customMetadata[$key])) {
            return $this->customMetadata[$key];
        }

        $meta = stream_get_meta_data($this->stream);

        return isset($meta[$key]) ? $meta[$key] : null;
    }
}
