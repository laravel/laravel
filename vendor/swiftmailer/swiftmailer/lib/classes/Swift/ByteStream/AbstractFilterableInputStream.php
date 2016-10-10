<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Provides the base functionality for an InputStream supporting filters.
 *
 * @author Chris Corbyn
 */
abstract class Swift_ByteStream_AbstractFilterableInputStream implements Swift_InputByteStream, Swift_Filterable
{
    /**
     * Write sequence.
     */
    protected $_sequence = 0;

    /**
     * StreamFilters.
     */
    private $_filters = array();

    /**
     * A buffer for writing.
     */
    private $_writeBuffer = '';

    /**
     * Bound streams.
     *
     * @var Swift_InputByteStream[]
     */
    private $_mirrors = array();

    /**
     * Commit the given bytes to the storage medium immediately.
     *
     * @param string $bytes
     */
    abstract protected function _commit($bytes);

    /**
     * Flush any buffers/content with immediate effect.
     */
    abstract protected function _flush();

    /**
     * Add a StreamFilter to this InputByteStream.
     *
     * @param Swift_StreamFilter $filter
     * @param string             $key
     */
    public function addFilter(Swift_StreamFilter $filter, $key)
    {
        $this->_filters[$key] = $filter;
    }

    /**
     * Remove an already present StreamFilter based on its $key.
     *
     * @param string $key
     */
    public function removeFilter($key)
    {
        unset($this->_filters[$key]);
    }

    /**
     * Writes $bytes to the end of the stream.
     *
     * @param string $bytes
     *
     * @throws Swift_IoException
     *
     * @return int
     */
    public function write($bytes)
    {
        $this->_writeBuffer .= $bytes;
        foreach ($this->_filters as $filter) {
            if ($filter->shouldBuffer($this->_writeBuffer)) {
                return;
            }
        }
        $this->_doWrite($this->_writeBuffer);

        return ++$this->_sequence;
    }

    /**
     * For any bytes that are currently buffered inside the stream, force them
     * off the buffer.
     *
     * @throws Swift_IoException
     */
    public function commit()
    {
        $this->_doWrite($this->_writeBuffer);
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
                if ($this->_writeBuffer !== '') {
                    $stream->write($this->_writeBuffer);
                }
                unset($this->_mirrors[$k]);
            }
        }
    }

    /**
     * Flush the contents of the stream (empty it) and set the internal pointer
     * to the beginning.
     *
     * @throws Swift_IoException
     */
    public function flushBuffers()
    {
        if ($this->_writeBuffer !== '') {
            $this->_doWrite($this->_writeBuffer);
        }
        $this->_flush();

        foreach ($this->_mirrors as $stream) {
            $stream->flushBuffers();
        }
    }

    /** Run $bytes through all filters */
    private function _filter($bytes)
    {
        foreach ($this->_filters as $filter) {
            $bytes = $filter->filter($bytes);
        }

        return $bytes;
    }

    /** Just write the bytes to the stream */
    private function _doWrite($bytes)
    {
        $this->_commit($this->_filter($bytes));

        foreach ($this->_mirrors as $stream) {
            $stream->write($bytes);
        }

        $this->_writeBuffer = '';
    }
}
