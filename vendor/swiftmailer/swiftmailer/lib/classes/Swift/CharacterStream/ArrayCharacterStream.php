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
 * @author Chris Corbyn
 */
class Swift_CharacterStream_ArrayCharacterStream implements Swift_CharacterStream
{
    /** A map of byte values and their respective characters */
    private static $_charMap;

    /** A map of characters and their derivative byte values */
    private static $_byteMap;

    /** The char reader (lazy-loaded) for the current charset */
    private $_charReader;

    /** A factory for creating CharacterReader instances */
    private $_charReaderFactory;

    /** The character set this stream is using */
    private $_charset;

    /** Array of characters */
    private $_array = array();

    /** Size of the array of character */
    private $_array_size = array();

    /** The current character offset in the stream */
    private $_offset = 0;

    /**
     * Create a new CharacterStream with the given $chars, if set.
     *
     * @param Swift_CharacterReaderFactory $factory for loading validators
     * @param string                       $charset used in the stream
     */
    public function __construct(Swift_CharacterReaderFactory $factory, $charset)
    {
        self::_initializeMaps();
        $this->setCharacterReaderFactory($factory);
        $this->setCharacterSet($charset);
    }

    /**
     * Set the character set used in this CharacterStream.
     *
     * @param string $charset
     */
    public function setCharacterSet($charset)
    {
        $this->_charset = $charset;
        $this->_charReader = null;
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
     * Overwrite this character stream using the byte sequence in the byte stream.
     *
     * @param Swift_OutputByteStream $os output stream to read from
     */
    public function importByteStream(Swift_OutputByteStream $os)
    {
        if (!isset($this->_charReader)) {
            $this->_charReader = $this->_charReaderFactory
                ->getReaderFor($this->_charset);
        }

        $startLength = $this->_charReader->getInitialByteSize();
        while (false !== $bytes = $os->read($startLength)) {
            $c = array();
            for ($i = 0, $len = strlen($bytes); $i < $len; ++$i) {
                $c[] = self::$_byteMap[$bytes[$i]];
            }
            $size = count($c);
            $need = $this->_charReader
                ->validateByteSequence($c, $size);
            if ($need > 0 &&
                false !== $bytes = $os->read($need)) {
                for ($i = 0, $len = strlen($bytes); $i < $len; ++$i) {
                    $c[] = self::$_byteMap[$bytes[$i]];
                }
            }
            $this->_array[] = $c;
            ++$this->_array_size;
        }
    }

    /**
     * Import a string a bytes into this CharacterStream, overwriting any existing
     * data in the stream.
     *
     * @param string $string
     */
    public function importString($string)
    {
        $this->flushContents();
        $this->write($string);
    }

    /**
     * Read $length characters from the stream and move the internal pointer
     * $length further into the stream.
     *
     * @param int $length
     *
     * @return string
     */
    public function read($length)
    {
        if ($this->_offset == $this->_array_size) {
            return false;
        }

        // Don't use array slice
        $arrays = array();
        $end = $length + $this->_offset;
        for ($i = $this->_offset; $i < $end; ++$i) {
            if (!isset($this->_array[$i])) {
                break;
            }
            $arrays[] = $this->_array[$i];
        }
        $this->_offset += $i - $this->_offset; // Limit function calls
        $chars = false;
        foreach ($arrays as $array) {
            $chars .= implode('', array_map('chr', $array));
        }

        return $chars;
    }

    /**
     * Read $length characters from the stream and return a 1-dimensional array
     * containing there octet values.
     *
     * @param int $length
     *
     * @return integer[]
     */
    public function readBytes($length)
    {
        if ($this->_offset == $this->_array_size) {
            return false;
        }
        $arrays = array();
        $end = $length + $this->_offset;
        for ($i = $this->_offset; $i < $end; ++$i) {
            if (!isset($this->_array[$i])) {
                break;
            }
            $arrays[] = $this->_array[$i];
        }
        $this->_offset += ($i - $this->_offset); // Limit function calls

        return call_user_func_array('array_merge', $arrays);
    }

    /**
     * Write $chars to the end of the stream.
     *
     * @param string $chars
     */
    public function write($chars)
    {
        if (!isset($this->_charReader)) {
            $this->_charReader = $this->_charReaderFactory->getReaderFor(
                $this->_charset);
        }

        $startLength = $this->_charReader->getInitialByteSize();

        $fp = fopen('php://memory', 'w+b');
        fwrite($fp, $chars);
        unset($chars);
        fseek($fp, 0, SEEK_SET);

        $buffer = array(0);
        $buf_pos = 1;
        $buf_len = 1;
        $has_datas = true;
        do {
            $bytes = array();
            // Buffer Filing
            if ($buf_len - $buf_pos < $startLength) {
                $buf = array_splice($buffer, $buf_pos);
                $new = $this->_reloadBuffer($fp, 100);
                if ($new) {
                    $buffer = array_merge($buf, $new);
                    $buf_len = count($buffer);
                    $buf_pos = 0;
                } else {
                    $has_datas = false;
                }
            }
            if ($buf_len - $buf_pos > 0) {
                $size = 0;
                for ($i = 0; $i < $startLength && isset($buffer[$buf_pos]); ++$i) {
                    ++$size;
                    $bytes[] = $buffer[$buf_pos++];
                }
                $need = $this->_charReader->validateByteSequence(
                    $bytes, $size);
                if ($need > 0) {
                    if ($buf_len - $buf_pos < $need) {
                        $new = $this->_reloadBuffer($fp, $need);

                        if ($new) {
                            $buffer = array_merge($buffer, $new);
                            $buf_len = count($buffer);
                        }
                    }
                    for ($i = 0; $i < $need && isset($buffer[$buf_pos]); ++$i) {
                        $bytes[] = $buffer[$buf_pos++];
                    }
                }
                $this->_array[] = $bytes;
                ++$this->_array_size;
            }
        } while ($has_datas);

        fclose($fp);
    }

    /**
     * Move the internal pointer to $charOffset in the stream.
     *
     * @param int $charOffset
     */
    public function setPointer($charOffset)
    {
        if ($charOffset > $this->_array_size) {
            $charOffset = $this->_array_size;
        } elseif ($charOffset < 0) {
            $charOffset = 0;
        }
        $this->_offset = $charOffset;
    }

    /**
     * Empty the stream and reset the internal pointer.
     */
    public function flushContents()
    {
        $this->_offset = 0;
        $this->_array = array();
        $this->_array_size = 0;
    }

    private function _reloadBuffer($fp, $len)
    {
        if (!feof($fp) && ($bytes = fread($fp, $len)) !== false) {
            $buf = array();
            for ($i = 0, $len = strlen($bytes); $i < $len; ++$i) {
                $buf[] = self::$_byteMap[$bytes[$i]];
            }

            return $buf;
        }

        return false;
    }

    private static function _initializeMaps()
    {
        if (!isset(self::$_charMap)) {
            self::$_charMap = array();
            for ($byte = 0; $byte < 256; ++$byte) {
                self::$_charMap[$byte] = chr($byte);
            }
            self::$_byteMap = array_flip(self::$_charMap);
        }
    }
}
