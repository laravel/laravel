<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Allows reading and writing of bytes to and from a file.
 *
 * @author Chris Corbyn
 */
class Swift_ByteStream_FileByteStream extends Swift_ByteStream_AbstractFilterableInputStream implements Swift_FileStream
{
    /** The internal pointer offset */
    private $_offset = 0;

    /** The path to the file */
    private $_path;

    /** The mode this file is opened in for writing */
    private $_mode;

    /** A lazy-loaded resource handle for reading the file */
    private $_reader;

    /** A lazy-loaded resource handle for writing the file */
    private $_writer;

    /** If magic_quotes_runtime is on, this will be true */
    private $_quotes = false;

    /** If stream is seekable true/false, or null if not known */
    private $_seekable = null;

    /**
     * Create a new FileByteStream for $path.
     *
     * @param string $path
     * @param bool   $writable if true
     */
    public function __construct($path, $writable = false)
    {
        if (empty($path)) {
            throw new Swift_IoException('The path cannot be empty');
        }
        $this->_path = $path;
        $this->_mode = $writable ? 'w+b' : 'rb';

        if (function_exists('get_magic_quotes_runtime') && @get_magic_quotes_runtime() == 1) {
            $this->_quotes = true;
        }
    }

    /**
     * Get the complete path to the file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
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
     * @throws Swift_IoException
     *
     * @return string|bool
     */
    public function read($length)
    {
        $fp = $this->_getReadHandle();
        if (!feof($fp)) {
            if ($this->_quotes) {
                ini_set('magic_quotes_runtime', 0);
            }
            $bytes = fread($fp, $length);
            if ($this->_quotes) {
                ini_set('magic_quotes_runtime', 1);
            }
            $this->_offset = ftell($fp);

            // If we read one byte after reaching the end of the file
            // feof() will return false and an empty string is returned
            if ($bytes === '' && feof($fp)) {
                $this->_resetReadHandle();

                return false;
            }

            return $bytes;
        }

        $this->_resetReadHandle();

        return false;
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
        if (isset($this->_reader)) {
            $this->_seekReadStreamToPosition($byteOffset);
        }
        $this->_offset = $byteOffset;
    }

    /** Just write the bytes to the file */
    protected function _commit($bytes)
    {
        fwrite($this->_getWriteHandle(), $bytes);
        $this->_resetReadHandle();
    }

    /** Not used */
    protected function _flush()
    {
    }

    /** Get the resource for reading */
    private function _getReadHandle()
    {
        if (!isset($this->_reader)) {
            if (!$this->_reader = fopen($this->_path, 'rb')) {
                throw new Swift_IoException(
                    'Unable to open file for reading ['.$this->_path.']'
                );
            }
            if ($this->_offset != 0) {
                $this->_getReadStreamSeekableStatus();
                $this->_seekReadStreamToPosition($this->_offset);
            }
        }

        return $this->_reader;
    }

    /** Get the resource for writing */
    private function _getWriteHandle()
    {
        if (!isset($this->_writer)) {
            if (!$this->_writer = fopen($this->_path, $this->_mode)) {
                throw new Swift_IoException(
                    'Unable to open file for writing ['.$this->_path.']'
                );
            }
        }

        return $this->_writer;
    }

    /** Force a reload of the resource for reading */
    private function _resetReadHandle()
    {
        if (isset($this->_reader)) {
            fclose($this->_reader);
            $this->_reader = null;
        }
    }

    /** Check if ReadOnly Stream is seekable */
    private function _getReadStreamSeekableStatus()
    {
        $metas = stream_get_meta_data($this->_reader);
        $this->_seekable = $metas['seekable'];
    }

    /** Streams in a readOnly stream ensuring copy if needed */
    private function _seekReadStreamToPosition($offset)
    {
        if ($this->_seekable === null) {
            $this->_getReadStreamSeekableStatus();
        }
        if ($this->_seekable === false) {
            $currentPos = ftell($this->_reader);
            if ($currentPos < $offset) {
                $toDiscard = $offset - $currentPos;
                fread($this->_reader, $toDiscard);

                return;
            }
            $this->_copyReadStream();
        }
        fseek($this->_reader, $offset, SEEK_SET);
    }

    /** Copy a readOnly Stream to ensure seekability */
    private function _copyReadStream()
    {
        if ($tmpFile = fopen('php://temp/maxmemory:4096', 'w+b')) {
            /* We have opened a php:// Stream Should work without problem */
        } elseif (function_exists('sys_get_temp_dir') && is_writable(sys_get_temp_dir()) && ($tmpFile = tmpfile())) {
            /* We have opened a tmpfile */
        } else {
            throw new Swift_IoException('Unable to copy the file to make it seekable, sys_temp_dir is not writable, php://memory not available');
        }
        $currentPos = ftell($this->_reader);
        fclose($this->_reader);
        $source = fopen($this->_path, 'rb');
        if (!$source) {
            throw new Swift_IoException('Unable to open file for copying ['.$this->_path.']');
        }
        fseek($tmpFile, 0, SEEK_SET);
        while (!feof($source)) {
            fwrite($tmpFile, fread($source, 4096));
        }
        fseek($tmpFile, $currentPos, SEEK_SET);
        fclose($source);
        $this->_reader = $tmpFile;
    }
}
