<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A CharacterStream implementation which stores characters in an internal array.
 *
 * @author Xavier De Cock <xdecock@gmail.com>
 */
class Swift_CharacterStream_NgCharacterStream implements Swift_CharacterStream
{
    /**
     * The char reader (lazy-loaded) for the current charset.
     *
     * @var Swift_CharacterReader
     */
    private $_charReader;

    /**
     * A factory for creating CharacterReader instances.
     *
     * @var Swift_CharacterReaderFactory
     */
    private $_charReaderFactory;

    /**
     * The character set this stream is using.
     *
     * @var string
     */
    private $_charset;

    /**
     * The data's stored as-is.
     *
     * @var string
     */
    private $_datas = '';

    /**
     * Number of bytes in the stream.
     *
     * @var int
     */
    private $_datasSize = 0;

    /**
     * Map.
     *
     * @var mixed
     */
    private $_map;

    /**
     * Map Type.
     *
     * @var int
     */
    private $_mapType = 0;

    /**
     * Number of characters in the stream.
     *
     * @var int
     */
    private $_charCount = 0;

    /**
     * Position in the stream.
     *
     * @var int
     */
    private $_currentPos = 0;

    /**
     * Constructor.
     *
     * @param Swift_CharacterReaderFactory $factory
     * @param string                       $charset
     */
    public function __construct(Swift_CharacterReaderFactory $factory, $charset)
    {
        $this->setCharacterReaderFactory($factory);
        $this->setCharacterSet($charset);
    }

    /* -- Changing parameters of the stream -- */

    /**
     * Set the character set used in this CharacterStream.
     *
     * @param string $charset
     */
    public function setCharacterSet($charset)
    {
        $this->_charset = $charset;
        $this->_charReader = null;
        $this->_mapType = 0;
    }

    /**
     * Set the CharacterReaderFactory for multi charset support.
     *
     * @param Swift_CharacterReaderFactory $factory
     */
    public function setCharacterReaderFactory(Swift_CharacterReaderFactory $factory)
    {
        $this->_charReaderFactory = $factory;
    }

    /**
     * @see Swift_CharacterStream::flushContents()
     */
    public function flushContents()
    {
        $this->_datas = null;
        $this->_map = null;
        $this->_charCount = 0;
        $this->_currentPos = 0;
        $this->_datasSize = 0;
    }

    /**
     * @see Swift_CharacterStream::importByteStream()
     *
     * @param Swift_OutputByteStream $os
     */
    public function importByteStream(Swift_OutputByteStream $os)
    {
        $this->flushContents();
        $blocks = 512;
        $os->setReadPointer(0);
        while (false !== ($read = $os->read($blocks))) {
            $this->write($read);
        }
    }

    /**
     * @see Swift_CharacterStream::importString()
     *
     * @param string $string
     */
    public function importString($string)
    {
        $this->flushContents();
        $this->write($string);
    }

    /**
     * @see Swift_CharacterStream::read()
     *
     * @param int $length
     *
     * @return string
     */
    public function read($length)
    {
        if ($this->_currentPos >= $this->_charCount) {
            return false;
        }
        $ret = false;
        $length = ($this->_currentPos + $length > $this->_charCount)
          ? $this->_charCount - $this->_currentPos
          : $length;
        switch ($this->_mapType) {
            case Swift_CharacterReader::MAP_TYPE_FIXED_LEN:
                $len = $length * $this->_map;
                $ret = substr($this->_datas,
                        $this->_currentPos * $this->_map,
                        $len);
                $this->_currentPos += $length;
                break;

            case Swift_CharacterReader::MAP_TYPE_INVALID:
                $end = $this->_currentPos + $length;
                $end = $end > $this->_charCount
                    ? $this->_charCount
                    : $end;
                $ret = '';
                for (; $this->_currentPos < $length; ++$this->_currentPos) {
                    if (isset($this->_map[$this->_currentPos])) {
                        $ret .= '?';
                    } else {
                        $ret .= $this->_datas[$this->_currentPos];
                    }
                }
                break;

            case Swift_CharacterReader::MAP_TYPE_POSITIONS:
                $end = $this->_currentPos + $length;
                $end = $end > $this->_charCount
                    ? $this->_charCount
                    : $end;
                $ret = '';
                $start = 0;
                if ($this->_currentPos > 0) {
                    $start = $this->_map['p'][$this->_currentPos - 1];
                }
                $to = $start;
                for (; $this->_currentPos < $end; ++$this->_currentPos) {
                    if (isset($this->_map['i'][$this->_currentPos])) {
                        $ret .= substr($this->_datas, $start, $to - $start).'?';
                        $start = $this->_map['p'][$this->_currentPos];
                    } else {
                        $to = $this->_map['p'][$this->_currentPos];
                    }
                }
                $ret .= substr($this->_datas, $start, $to - $start);
                break;
        }

        return $ret;
    }

    /**
     * @see Swift_CharacterStream::readBytes()
     *
     * @param int $length
     *
     * @return integer[]
     */
    public function readBytes($length)
    {
        $read = $this->read($length);
        if ($read !== false) {
            $ret = array_map('ord', str_split($read, 1));

            return $ret;
        }

        return false;
    }

    /**
     * @see Swift_CharacterStream::setPointer()
     *
     * @param int $charOffset
     */
    public function setPointer($charOffset)
    {
        if ($this->_charCount < $charOffset) {
            $charOffset = $this->_charCount;
        }
        $this->_currentPos = $charOffset;
    }

    /**
     * @see Swift_CharacterStream::write()
     *
     * @param string $chars
     */
    public function write($chars)
    {
        if (!isset($this->_charReader)) {
            $this->_charReader = $this->_charReaderFactory->getReaderFor(
                $this->_charset);
            $this->_map = array();
            $this->_mapType = $this->_charReader->getMapType();
        }
        $ignored = '';
        $this->_datas .= $chars;
        $this->_charCount += $this->_charReader->getCharPositions(substr($this->_datas, $this->_datasSize), $this->_datasSize, $this->_map, $ignored);
        if ($ignored !== false) {
            $this->_datasSize = strlen($this->_datas) - strlen($ignored);
        } else {
            $this->_datasSize = strlen($this->_datas);
        }
    }
}
