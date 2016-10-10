<?php
/**
 *  base include file for SimpleTest
 *  @package    SimpleTest
 *  @subpackage MockObjects
 *  @version    $Id: socket.php 1788 2008-04-27 11:01:59Z pp11 $
 */

/**#@+
 * include SimpleTest files
 */
require_once(dirname(__FILE__) . '/compatibility.php');
/**#@-*/

/**
 *    Stashes an error for later. Useful for constructors
 *    until PHP gets exceptions.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleStickyError {
    private $error = 'Constructor not chained';

    /**
     *    Sets the error to empty.
     *    @access public
     */
    function __construct() {
        $this->clearError();
    }

    /**
     *    Test for an outstanding error.
     *    @return boolean           True if there is an error.
     *    @access public
     */
    function isError() {
        return ($this->error != '');
    }

    /**
     *    Accessor for an outstanding error.
     *    @return string     Empty string if no error otherwise
     *                       the error message.
     *    @access public
     */
    function getError() {
        return $this->error;
    }

    /**
     *    Sets the internal error.
     *    @param string       Error message to stash.
     *    @access protected
     */
    function setError($error) {
        $this->error = $error;
    }

    /**
     *    Resets the error state to no error.
     *    @access protected
     */
    function clearError() {
        $this->setError('');
    }
}

class SimpleFileSocket extends SimpleStickyError {
    private $handle;
    private $is_open = false;
    private $sent = '';
    private $block_size;

    /**
     *    Opens a socket for reading and writing.
     *    @param SimpleUrl $file       Target URI to fetch.
     *    @param integer $block_size   Size of chunk to read.
     *    @access public
     */
    function __construct($file, $block_size = 1024) {
        parent::__construct();
        if (! ($this->handle = $this->openFile($file, $error))) {
            $file_string = $file->asString();
            $this->setError("Cannot open [$file_string] with [$error]");
            return;
        }
        $this->is_open = true;
        $this->block_size = $block_size;
    }

    /**
     *    Writes some data to the socket and saves alocal copy.
     *    @param string $message       String to send to socket.
     *    @return boolean              True if successful.
     *    @access public
     */
    function write($message) {
        return true;
    }

    /**
     *    Reads data from the socket. The error suppresion
     *    is a workaround for PHP4 always throwing a warning
     *    with a secure socket.
     *    @return integer/boolean           Incoming bytes. False
     *                                     on error.
     *    @access public
     */
    function read() {
        $raw = @fread($this->handle, $this->block_size);
        if ($raw === false) {
            $this->setError('Cannot read from socket');
            $this->close();
        }
        return $raw;
    }

    /**
     *    Accessor for socket open state.
     *    @return boolean           True if open.
     *    @access public
     */
    function isOpen() {
        return $this->is_open;
    }

    /**
     *    Closes the socket preventing further reads.
     *    Cannot be reopened once closed.
     *    @return boolean           True if successful.
     *    @access public
     */
    function close() {
        if (!$this->is_open) return false;
        $this->is_open = false;
        return fclose($this->handle);
    }

    /**
     *    Accessor for content so far.
     *    @return string        Bytes sent only.
     *    @access public
     */
    function getSent() {
        return $this->sent;
    }

    /**
     *    Actually opens the low level socket.
     *    @param SimpleUrl $file       SimpleUrl file target.
     *    @param string $error         Recipient of error message.
     *    @param integer $timeout      Maximum time to wait for connection.
     *    @access protected
     */
    protected function openFile($file, &$error) {
        return @fopen($file->asString(), 'r');
    }
}

/**
 *    Wrapper for TCP/IP socket.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleSocket extends SimpleStickyError {
    private $handle;
    private $is_open = false;
    private $sent = '';
    private $lock_size;

    /**
     *    Opens a socket for reading and writing.
     *    @param string $host          Hostname to send request to.
     *    @param integer $port         Port on remote machine to open.
     *    @param integer $timeout      Connection timeout in seconds.
     *    @param integer $block_size   Size of chunk to read.
     *    @access public
     */
    function __construct($host, $port, $timeout, $block_size = 255) {
        parent::__construct();
        if (! ($this->handle = $this->openSocket($host, $port, $error_number, $error, $timeout))) {
            $this->setError("Cannot open [$host:$port] with [$error] within [$timeout] seconds");
            return;
        }
        $this->is_open = true;
        $this->block_size = $block_size;
        SimpleTestCompatibility::setTimeout($this->handle, $timeout);
    }

    /**
     *    Writes some data to the socket and saves alocal copy.
     *    @param string $message       String to send to socket.
     *    @return boolean              True if successful.
     *    @access public
     */
    function write($message) {
        if ($this->isError() || ! $this->isOpen()) {
            return false;
        }
        $count = fwrite($this->handle, $message);
        if (! $count) {
            if ($count === false) {
                $this->setError('Cannot write to socket');
                $this->close();
            }
            return false;
        }
        fflush($this->handle);
        $this->sent .= $message;
        return true;
    }

    /**
     *    Reads data from the socket. The error suppresion
     *    is a workaround for PHP4 always throwing a warning
     *    with a secure socket.
     *    @return integer/boolean           Incoming bytes. False
     *                                     on error.
     *    @access public
     */
    function read() {
        if ($this->isError() || ! $this->isOpen()) {
            return false;
        }
        $raw = @fread($this->handle, $this->block_size);
        if ($raw === false) {
            $this->setError('Cannot read from socket');
            $this->close();
        }
        return $raw;
    }

    /**
     *    Accessor for socket open state.
     *    @return boolean           True if open.
     *    @access public
     */
    function isOpen() {
        return $this->is_open;
    }

    /**
     *    Closes the socket preventing further reads.
     *    Cannot be reopened once closed.
     *    @return boolean           True if successful.
     *    @access public
     */
    function close() {
        $this->is_open = false;
        return fclose($this->handle);
    }

    /**
     *    Accessor for content so far.
     *    @return string        Bytes sent only.
     *    @access public
     */
    function getSent() {
        return $this->sent;
    }

    /**
     *    Actually opens the low level socket.
     *    @param string $host          Host to connect to.
     *    @param integer $port         Port on host.
     *    @param integer $error_number Recipient of error code.
     *    @param string $error         Recipoent of error message.
     *    @param integer $timeout      Maximum time to wait for connection.
     *    @access protected
     */
    protected function openSocket($host, $port, &$error_number, &$error, $timeout) {
        return @fsockopen($host, $port, $error_number, $error, $timeout);
    }
}

/**
 *    Wrapper for TCP/IP socket over TLS.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleSecureSocket extends SimpleSocket {

    /**
     *    Opens a secure socket for reading and writing.
     *    @param string $host      Hostname to send request to.
     *    @param integer $port     Port on remote machine to open.
     *    @param integer $timeout  Connection timeout in seconds.
     *    @access public
     */
    function __construct($host, $port, $timeout) {
        parent::__construct($host, $port, $timeout);
    }

    /**
     *    Actually opens the low level socket.
     *    @param string $host          Host to connect to.
     *    @param integer $port         Port on host.
     *    @param integer $error_number Recipient of error code.
     *    @param string $error         Recipient of error message.
     *    @param integer $timeout      Maximum time to wait for connection.
     *    @access protected
     */
    function openSocket($host, $port, &$error_number, &$error, $timeout) {
        return parent::openSocket("tls://$host", $port, $error_number, $error, $timeout);
    }
}
?>