<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Allows reading and writing of bytes to and from an array.
 *
 * @author Chris Corbyn
 */
class Swift_ByteStream_ArrayByteStream implements Swift_InputByteStream, Swift_OutputByteStream
{
    /**
     * The internal stack of bytes.
     *
     * @var string[]
     */
    private $_array = array();

    /**
     * The size of the stack.
     *
     * @var int
     */
    private $_arraySize = 0;

    /**
     * The internal pointer offset.
     *
     * @var int
     */
    private $_offset = 0;

    /**
     * Bound streams.
     *
     * @var Swift_InputByteStream[]
     */
    private $_mirrors = array();

    /**
     * Create a new ArrayByteStream.
     *
     * If $stack is given the stream will be populated with the bytes it contains.
     *
     * @param mixed $stack of bytes in string or array form, optional
     */
    public function __construct($stack = null)
    {
        if (is_array($stack)) {
            $this->_array = $stack;
            $this->_arraySize = count($stack);
        } elseif (is_string($stack)) {
            $this->write($stack);
        } else {
            $this->_array = array();
        }
    }

    /**
     * Reads $length bytes from the stream into a string and moves the pointer
     * through the stream by $length.
     *
     * If less bytes exist than are requested the
     * remaining bytes are given instead. If no bytes are remaining at all, boolean
     * false is returned.
     *
     * @param int $length
     *
     * @return string
     */
    public function read($length)
    {
        if ($this->_offset == $this->_arraySize) {
            return false;
        }

        // Don't use array slice
        $end = $length + $this->_offset;
        $end = $this->_arraySize < $end
            ? $this->_arraySize
            : $end;
        $ret = '';
        for (; $this->_offset < $end; ++$this->_offset) {
            $ret .= $this->_array[$this->_offset];
        }

        return $ret;
    }

    /**
     * Writes $bytes to the end of the stream.
     *
     * @param string $bytes
     */
    public function write($bytes)
    {
        $to_add = str_split($bytes);
        foreach ($to_add as $value) {
            $this->_array[] = $value;
        }
        $this->_arraySize = count($this->_array);

        foreach ($this->_mirrors as $stream) {
            $stream->write($bytes);
        }
    }

    /**
     * Not used.
     */
    public function commit()
    {
    }

    /**
     * Attach $is to this stream.
     *
     * The stream acts as an observer, receiving all data that is written.
     * All {@link write()} and {@link flushBuffers()} operations will be mirrored.
     *
     * @param Swift_InputByteStream $is
     */
    public function bind(Swift_InputByteStream $is)
    {
        $this->_mirrors[] = $is;
    }

    /**
     * Remove an already bound stream.
     *
     * If $is is not bound, no errors will be raised.
     * If the stream currently has any buffered data it will be written to $is
     * before unbinding occurs.
     *
     * @param Swift_InputByteStream $is
     */
    public function unbind(Swift_InputByteStream $is)
    {
        foreach ($this->_mirrors as $k => $stream) {
            if ($is === $stream) {
                unset($this->_mirrors[$k]);
            }
        }
    }

    /**
     * Move the internal read pointer to $byteOffset in the stream.
     *
     * @param int $byteOffset
     *
     * @return bool
     */
    public function setReadPointer($byteOffset)
    {
        if ($byteOffset > $this->_arraySize) {
            $byteOffset = $this->_arraySize;
        } elseif ($byteOffset < 0) {
            $byteOffset = 0;
        }

        $this->_offset = $byteOffset;
    }

    /**
     * Flush the contents of the stream (empty it) and set the internal pointer
     * to the beginning.
     */
    public function flushBuffers()
    {
        $this->_offset = 0;
        $this->_array = array();
        $this->_arraySize = 0;

        foreach ($this->_mirrors as $stream) {
            $stream->flushBuffers();
        }
    }
}
