<?php
namespace GuzzleHttp\Stream;

use GuzzleHttp\Stream\Exception\SeekException;

/**
 * Static utility class because PHP's autoloaders don't support the concept
 * of namespaced function autoloading.
 */
class Utils
{
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
     * @throws \RuntimeException if the file cannot be opened
     */
    public static function open($filename, $mode)
    {
        $ex = null;
        set_error_handler(function () use ($filename, $mode, &$ex) {
            $ex = new \RuntimeException(sprintf(
                'Unable to open %s using mode %s: %s',
                $filename,
                $mode,
                func_get_args()[1]
            ));
        });

        $handle = fopen($filename, $mode);
        restore_error_handler();

        if ($ex) {
            /** @var $ex \RuntimeException */
            throw $ex;
        }

        return $handle;
    }

    /**
     * Copy the contents of a stream into a string until the given number of
     * bytes have been read.
     *
     * @param StreamInterface $stream Stream to read
     * @param int             $maxLen Maximum number of bytes to read. Pass -1
     *                                to read the entire stream.
     * @return string
     */
    public static function copyToString(StreamInterface $stream, $maxLen = -1)
    {
        $buffer = '';

        if ($maxLen === -1) {
            while (!$stream->eof()) {
                $buf = $stream->read(1048576);
                if ($buf === false) {
                    break;
                }
                $buffer .= $buf;
            }
            return $buffer;
        }

        $len = 0;
        while (!$stream->eof() && $len < $maxLen) {
            $buf = $stream->read($maxLen - $len);
            if ($buf === false) {
                break;
            }
            $buffer .= $buf;
            $len = strlen($buffer);
        }

        return $buffer;
    }

    /**
     * Copy the contents of a stream into another stream until the given number
     * of bytes have been read.
     *
     * @param StreamInterface $source Stream to read from
     * @param StreamInterface $dest   Stream to write to
     * @param int             $maxLen Maximum number of bytes to read. Pass -1
     *                                to read the entire stream.
     */
    public static function copyToStream(
        StreamInterface $source,
        StreamInterface $dest,
        $maxLen = -1
    ) {
        if ($maxLen === -1) {
            while (!$source->eof()) {
                if (!$dest->write($source->read(1048576))) {
                    break;
                }
            }
            return;
        }

        $bytes = 0;
        while (!$source->eof()) {
            $buf = $source->read($maxLen - $bytes);
            if (!($len = strlen($buf))) {
                break;
            }
            $bytes += $len;
            $dest->write($buf);
            if ($bytes == $maxLen) {
                break;
            }
        }
    }

    /**
     * Calculate a hash of a Stream
     *
     * @param StreamInterface $stream    Stream to calculate the hash for
     * @param string          $algo      Hash algorithm (e.g. md5, crc32, etc)
     * @param bool            $rawOutput Whether or not to use raw output
     *
     * @return string Returns the hash of the stream
     * @throws SeekException
     */
    public static function hash(
        StreamInterface $stream,
        $algo,
        $rawOutput = false
    ) {
        $pos = $stream->tell();

        if ($pos > 0 && !$stream->seek(0)) {
            throw new SeekException($stream);
        }

        $ctx = hash_init($algo);
        while (!$stream->eof()) {
            hash_update($ctx, $stream->read(1048576));
        }

        $out = hash_final($ctx, (bool) $rawOutput);
        $stream->seek($pos);

        return $out;
    }

    /**
     * Read a line from the stream up to the maximum allowed buffer length
     *
     * @param StreamInterface $stream    Stream to read from
     * @param int             $maxLength Maximum buffer length
     *
     * @return string|bool
     */
    public static function readline(StreamInterface $stream, $maxLength = null)
    {
        $buffer = '';
        $size = 0;

        while (!$stream->eof()) {
            if (false === ($byte = $stream->read(1))) {
                return $buffer;
            }
            $buffer .= $byte;
            // Break when a new line is found or the max length - 1 is reached
            if ($byte == PHP_EOL || ++$size == $maxLength - 1) {
                break;
            }
        }

        return $buffer;
    }

    /**
     * Alias of GuzzleHttp\Stream\Stream::factory.
     *
     * @param mixed $resource Resource to create
     * @param array $options  Associative array of stream options defined in
     *                        {@see \GuzzleHttp\Stream\Stream::__construct}
     *
     * @return StreamInterface
     *
     * @see GuzzleHttp\Stream\Stream::factory
     * @see GuzzleHttp\Stream\Stream::__construct
     */
    public static function create($resource, array $options = [])
    {
        return Stream::factory($resource, $options);
    }
}
