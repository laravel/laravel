<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A generic IoBuffer implementation supporting remote sockets and local processes.
 *
 * @author Chris Corbyn
 */
class Swift_Transport_StreamBuffer extends Swift_ByteStream_AbstractFilterableInputStream implements Swift_Transport_IoBuffer
{
    /** A primary socket */
    private $_stream;

    /** The input stream */
    private $_in;

    /** The output stream */
    private $_out;

    /** Buffer initialization parameters */
    private $_params = array();

    /** The ReplacementFilterFactory */
    private $_replacementFactory;

    /** Translations performed on data being streamed into the buffer */
    private $_translations = array();

    /**
     * Create a new StreamBuffer using $replacementFactory for transformations.
     *
     * @param Swift_ReplacementFilterFactory $replacementFactory
     */
    public function __construct(Swift_ReplacementFilterFactory $replacementFactory)
    {
        $this->_replacementFactory = $replacementFactory;
    }

    /**
     * Perform any initialization needed, using the given $params.
     *
     * Parameters will vary depending upon the type of IoBuffer used.
     *
     * @param array $params
     */
    public function initialize(array $params)
    {
        $this->_params = $params;
        switch ($params['type']) {
            case self::TYPE_PROCESS:
                $this->_establishProcessConnection();
                break;
            case self::TYPE_SOCKET:
            default:
                $this->_establishSocketConnection();
                break;
        }
    }

    /**
     * Set an individual param on the buffer (e.g. switching to SSL).
     *
     * @param string $param
     * @param mixed  $value
     */
    public function setParam($param, $value)
    {
        if (isset($this->_stream)) {
            switch ($param) {
                case 'timeout':
                    if ($this->_stream) {
                        stream_set_timeout($this->_stream, $value);
                    }
                    break;

                case 'blocking':
                    if ($this->_stream) {
                        stream_set_blocking($this->_stream, 1);
                    }

            }
        }
        $this->_params[$param] = $value;
    }

    public function startTLS()
    {
        return stream_socket_enable_crypto($this->_stream, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
    }

    /**
     * Perform any shutdown logic needed.
     */
    public function terminate()
    {
        if (isset($this->_stream)) {
            switch ($this->_params['type']) {
                case self::TYPE_PROCESS:
                    fclose($this->_in);
                    fclose($this->_out);
                    proc_close($this->_stream);
                    break;
                case self::TYPE_SOCKET:
                default:
                    fclose($this->_stream);
                    break;
            }
        }
        $this->_stream = null;
        $this->_out = null;
        $this->_in = null;
    }

    /**
     * Set an array of string replacements which should be made on data written
     * to the buffer.
     *
     * This could replace LF with CRLF for example.
     *
     * @param string[] $replacements
     */
    public function setWriteTranslations(array $replacements)
    {
        foreach ($this->_translations as $search => $replace) {
            if (!isset($replacements[$search])) {
                $this->removeFilter($search);
                unset($this->_translations[$search]);
            }
        }

        foreach ($replacements as $search => $replace) {
            if (!isset($this->_translations[$search])) {
                $this->addFilter(
                    $this->_replacementFactory->createFilter($search, $replace), $search
                    );
                $this->_translations[$search] = true;
            }
        }
    }

    /**
     * Get a line of output (including any CRLF).
     *
     * The $sequence number comes from any writes and may or may not be used
     * depending upon the implementation.
     *
     * @param int $sequence of last write to scan from
     *
     * @throws Swift_IoException
     *
     * @return string
     */
    public function readLine($sequence)
    {
        if (isset($this->_out) && !feof($this->_out)) {
            $line = fgets($this->_out);
            if (strlen($line) == 0) {
                $metas = stream_get_meta_data($this->_out);
                if ($metas['timed_out']) {
                    throw new Swift_IoException(
                        'Connection to '.
                            $this->_getReadConnectionDescription().
                        ' Timed Out'
                    );
                }
            }

            return $line;
        }
    }

    /**
     * Reads $length bytes from the stream into a string and moves the pointer
     * through the stream by $length.
     *
     * If less bytes exist than are requested the remaining bytes are given instead.
     * If no bytes are remaining at all, boolean false is returned.
     *
     * @param int $length
     *
     * @throws Swift_IoException
     *
     * @return string|bool
     */
    public function read($length)
    {
        if (isset($this->_out) && !feof($this->_out)) {
            $ret = fread($this->_out, $length);
            if (strlen($ret) == 0) {
                $metas = stream_get_meta_data($this->_out);
                if ($metas['timed_out']) {
                    throw new Swift_IoException(
                        'Connection to '.
                            $this->_getReadConnectionDescription().
                        ' Timed Out'
                    );
                }
            }

            return $ret;
        }
    }

    /** Not implemented */
    public function setReadPointer($byteOffset)
    {
    }

    /** Flush the stream contents */
    protected function _flush()
    {
        if (isset($this->_in)) {
            fflush($this->_in);
        }
    }

    /** Write this bytes to the stream */
    protected function _commit($bytes)
    {
        if (isset($this->_in)) {
            $bytesToWrite = strlen($bytes);
            $totalBytesWritten = 0;

            while ($totalBytesWritten < $bytesToWrite) {
                $bytesWritten = fwrite($this->_in, substr($bytes, $totalBytesWritten));
                if (false === $bytesWritten || 0 === $bytesWritten) {
                    break;
                }

                $totalBytesWritten += $bytesWritten;
            }

            if ($totalBytesWritten > 0) {
                return ++$this->_sequence;
            }
        }
    }

    /**
     * Establishes a connection to a remote server.
     */
    private function _establishSocketConnection()
    {
        $host = $this->_params['host'];
        if (!empty($this->_params['protocol'])) {
            $host = $this->_params['protocol'].'://'.$host;
        }
        $timeout = 15;
        if (!empty($this->_params['timeout'])) {
            $timeout = $this->_params['timeout'];
        }
        $options = array();
        if (!empty($this->_params['sourceIp'])) {
            $options['socket']['bindto'] = $this->_params['sourceIp'].':0';
        }
        $this->_stream = @stream_socket_client($host.':'.$this->_params['port'], $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, stream_context_create($options));
        if (false === $this->_stream) {
            throw new Swift_TransportException(
                'Connection could not be established with host '.$this->_params['host'].
                ' ['.$errstr.' #'.$errno.']'
                );
        }
        if (!empty($this->_params['blocking'])) {
            stream_set_blocking($this->_stream, 1);
        } else {
            stream_set_blocking($this->_stream, 0);
        }
        stream_set_timeout($this->_stream, $timeout);
        $this->_in = &$this->_stream;
        $this->_out = &$this->_stream;
    }

    /**
     * Opens a process for input/output.
     */
    private function _establishProcessConnection()
    {
        $command = $this->_params['command'];
        $descriptorSpec = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
            );
        $this->_stream = proc_open($command, $descriptorSpec, $pipes);
        stream_set_blocking($pipes[2], 0);
        if ($err = stream_get_contents($pipes[2])) {
            throw new Swift_TransportException(
                'Process could not be started ['.$err.']'
                );
        }
        $this->_in = &$pipes[0];
        $this->_out = &$pipes[1];
    }

    private function _getReadConnectionDescription()
    {
        switch ($this->_params['type']) {
            case self::TYPE_PROCESS:
                return 'Process '.$this->_params['command'];
                break;

            case self::TYPE_SOCKET:
            default:
                $host = $this->_params['host'];
                if (!empty($this->_params['protocol'])) {
                    $host = $this->_params['protocol'].'://'.$host;
                }
                $host .= ':'.$this->_params['port'];

                return $host;
                break;
        }
    }
}
