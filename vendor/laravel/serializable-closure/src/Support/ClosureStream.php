<?php

namespace Laravel\SerializableClosure\Support;

#[\AllowDynamicProperties]
class ClosureStream
{
    /**
     * The stream protocol.
     *
     * @var string
     */
    const STREAM_PROTO = 'laravel-serializable-closure';

    /**
     * Checks if this stream is registered.
     *
     * @var bool
     */
    protected static $isRegistered = false;

    /**
     * The stream content.
     *
     * @var string
     */
    protected $content;

    /**
     * The stream content.
     *
     * @var int
     */
    protected $length;

    /**
     * The stream pointer.
     *
     * @var int
     */
    protected $pointer = 0;

    /**
     * Opens file or URL.
     *
     * @param  string  $path
     * @param  string  $mode
     * @param  string  $options
     * @param  string|null  $opened_path
     * @return bool
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $this->content = "<?php\nreturn ".substr($path, strlen(static::STREAM_PROTO.'://')).';';
        $this->length = strlen($this->content);

        return true;
    }

    /**
     * Read from stream.
     *
     * @param  int  $count
     * @return string
     */
    public function stream_read($count)
    {
        $value = substr($this->content, $this->pointer, $count);

        $this->pointer += $count;

        return $value;
    }

    /**
     * Tests for end-of-file on a file pointer.
     *
     * @return bool
     */
    public function stream_eof()
    {
        return $this->pointer >= $this->length;
    }

    /**
     * Change stream options.
     *
     * @param  int  $option
     * @param  int  $arg1
     * @param  int  $arg2
     * @return bool
     */
    public function stream_set_option($option, $arg1, $arg2)
    {
        return false;
    }

    /**
     * Retrieve information about a file resource.
     *
     * @return array|bool
     */
    public function stream_stat()
    {
        $stat = stat(__FILE__);
        // @phpstan-ignore-next-line
        $stat[7] = $stat['size'] = $this->length;

        return $stat;
    }

    /**
     * Retrieve information about a file.
     *
     * @param  string  $path
     * @param  int  $flags
     * @return array|bool
     */
    public function url_stat($path, $flags)
    {
        $stat = stat(__FILE__);
        // @phpstan-ignore-next-line
        $stat[7] = $stat['size'] = $this->length;

        return $stat;
    }

    /**
     * Seeks to specific location in a stream.
     *
     * @param  int  $offset
     * @param  int  $whence
     * @return bool
     */
    public function stream_seek($offset, $whence = SEEK_SET)
    {
        $crt = $this->pointer;

        switch ($whence) {
            case SEEK_SET:
                $this->pointer = $offset;
                break;
            case SEEK_CUR:
                $this->pointer += $offset;
                break;
            case SEEK_END:
                $this->pointer = $this->length + $offset;
                break;
        }

        if ($this->pointer < 0 || $this->pointer >= $this->length) {
            $this->pointer = $crt;

            return false;
        }

        return true;
    }

    /**
     * Retrieve the current position of a stream.
     *
     * @return int
     */
    public function stream_tell()
    {
        return $this->pointer;
    }

    /**
     * Registers the stream.
     *
     * @return void
     */
    public static function register()
    {
        if (! static::$isRegistered) {
            static::$isRegistered = stream_wrapper_register(static::STREAM_PROTO, __CLASS__);
        }
    }
}
