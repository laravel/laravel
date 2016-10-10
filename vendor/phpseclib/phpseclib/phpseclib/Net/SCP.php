<?php

/**
 * Pure-PHP implementation of SCP.
 *
 * PHP versions 4 and 5
 *
 * The API for this library is modeled after the API from PHP's {@link http://php.net/book.ftp FTP extension}.
 *
 * Here's a short example of how to use this library:
 * <code>
 * <?php
 *    include 'Net/SCP.php';
 *    include 'Net/SSH2.php';
 *
 *    $ssh = new Net_SSH2('www.domain.tld');
 *    if (!$ssh->login('username', 'password')) {
 *        exit('bad login');
 *    }

 *    $scp = new Net_SCP($ssh);
 *    $scp->put('abcd', str_repeat('x', 1024*1024));
 * ?>
 * </code>
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category  Net
 * @package   Net_SCP
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2010 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */

/**#@+
 * @access public
 * @see Net_SCP::put()
 */
/**
 * Reads data from a local file.
 */
define('NET_SCP_LOCAL_FILE', 1);
/**
 * Reads data from a string.
 */
define('NET_SCP_STRING',  2);
/**#@-*/

/**#@+
 * @access private
 * @see Net_SCP::_send()
 * @see Net_SCP::_receive()
 */
/**
 * SSH1 is being used.
 */
define('NET_SCP_SSH1', 1);
/**
 * SSH2 is being used.
 */
define('NET_SCP_SSH2',  2);
/**#@-*/

/**
 * Pure-PHP implementations of SCP.
 *
 * @package Net_SCP
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
class Net_SCP
{
    /**
     * SSH Object
     *
     * @var Object
     * @access private
     */
    var $ssh;

    /**
     * Packet Size
     *
     * @var Integer
     * @access private
     */
    var $packet_size;

    /**
     * Mode
     *
     * @var Integer
     * @access private
     */
    var $mode;

    /**
     * Default Constructor.
     *
     * Connects to an SSH server
     *
     * @param String $host
     * @param optional Integer $port
     * @param optional Integer $timeout
     * @return Net_SCP
     * @access public
     */
    function Net_SCP($ssh)
    {
        if (!is_object($ssh)) {
            return;
        }

        switch (strtolower(get_class($ssh))) {
            case 'net_ssh2':
                $this->mode = NET_SCP_SSH2;
                break;
            case 'net_ssh1':
                $this->packet_size = 50000;
                $this->mode = NET_SCP_SSH1;
                break;
            default:
                return;
        }

        $this->ssh = $ssh;
    }

    /**
     * Uploads a file to the SCP server.
     *
     * By default, Net_SCP::put() does not read from the local filesystem.  $data is dumped directly into $remote_file.
     * So, for example, if you set $data to 'filename.ext' and then do Net_SCP::get(), you will get a file, twelve bytes
     * long, containing 'filename.ext' as its contents.
     *
     * Setting $mode to NET_SCP_LOCAL_FILE will change the above behavior.  With NET_SCP_LOCAL_FILE, $remote_file will
     * contain as many bytes as filename.ext does on your local filesystem.  If your filename.ext is 1MB then that is how
     * large $remote_file will be, as well.
     *
     * Currently, only binary mode is supported.  As such, if the line endings need to be adjusted, you will need to take
     * care of that, yourself.
     *
     * @param String $remote_file
     * @param String $data
     * @param optional Integer $mode
     * @param optional Callable $callback
     * @return Boolean
     * @access public
     */
    function put($remote_file, $data, $mode = NET_SCP_STRING, $callback = null)
    {
        if (!isset($this->ssh)) {
            return false;
        }

        if (!$this->ssh->exec('scp -t ' . escapeshellarg($remote_file), false)) { // -t = to
            return false;
        }

        $temp = $this->_receive();
        if ($temp !== chr(0)) {
            return false;
        }

        if ($this->mode == NET_SCP_SSH2) {
            $this->packet_size = $this->ssh->packet_size_client_to_server[NET_SSH2_CHANNEL_EXEC] - 4;
        }

        $remote_file = basename($remote_file);

        if ($mode == NET_SCP_STRING) {
            $size = strlen($data);
        } else {
            if (!is_file($data)) {
                user_error("$data is not a valid file", E_USER_NOTICE);
                return false;
            }

            $fp = @fopen($data, 'rb');
            if (!$fp) {
                return false;
            }
            $size = filesize($data);
        }

        $this->_send('C0644 ' . $size . ' ' . $remote_file . "\n");

        $temp = $this->_receive();
        if ($temp !== chr(0)) {
            return false;
        }

        $sent = 0;
        while ($sent < $size) {
            $temp = $mode & NET_SCP_STRING ? substr($data, $sent, $this->packet_size) : fread($fp, $this->packet_size);
            $this->_send($temp);
            $sent+= strlen($temp);

            if (is_callable($callback)) {
                call_user_func($callback, $sent);
            }
        }
        $this->_close();

        if ($mode != NET_SCP_STRING) {
            fclose($fp);
        }

        return true;
    }

    /**
     * Downloads a file from the SCP server.
     *
     * Returns a string containing the contents of $remote_file if $local_file is left undefined or a boolean false if
     * the operation was unsuccessful.  If $local_file is defined, returns true or false depending on the success of the
     * operation
     *
     * @param String $remote_file
     * @param optional String $local_file
     * @return Mixed
     * @access public
     */
    function get($remote_file, $local_file = false)
    {
        if (!isset($this->ssh)) {
            return false;
        }

        if (!$this->ssh->exec('scp -f ' . escapeshellarg($remote_file), false)) { // -f = from
            return false;
        }

        $this->_send("\0");

        if (!preg_match('#(?<perms>[^ ]+) (?<size>\d+) (?<name>.+)#', rtrim($this->_receive()), $info)) {
            return false;
        }

        $this->_send("\0");

        $size = 0;

        if ($local_file !== false) {
            $fp = @fopen($local_file, 'wb');
            if (!$fp) {
                return false;
            }
        }

        $content = '';
        while ($size < $info['size']) {
            $data = $this->_receive();
            // SCP usually seems to split stuff out into 16k chunks
            $size+= strlen($data);

            if ($local_file === false) {
                $content.= $data;
            } else {
                fputs($fp, $data);
            }
        }

        $this->_close();

        if ($local_file !== false) {
            fclose($fp);
            return true;
        }

        return $content;
    }

    /**
     * Sends a packet to an SSH server
     *
     * @param String $data
     * @access private
     */
    function _send($data)
    {
        switch ($this->mode) {
            case NET_SCP_SSH2:
                $this->ssh->_send_channel_packet(NET_SSH2_CHANNEL_EXEC, $data);
                break;
            case NET_SCP_SSH1:
                $data = pack('CNa*', NET_SSH1_CMSG_STDIN_DATA, strlen($data), $data);
                $this->ssh->_send_binary_packet($data);
         }
    }

    /**
     * Receives a packet from an SSH server
     *
     * @return String
     * @access private
     */
    function _receive()
    {
        switch ($this->mode) {
            case NET_SCP_SSH2:
                return $this->ssh->_get_channel_packet(NET_SSH2_CHANNEL_EXEC, true);
            case NET_SCP_SSH1:
                if (!$this->ssh->bitmap) {
                    return false;
                }
                while (true) {
                    $response = $this->ssh->_get_binary_packet();
                    switch ($response[NET_SSH1_RESPONSE_TYPE]) {
                        case NET_SSH1_SMSG_STDOUT_DATA:
                            extract(unpack('Nlength', $response[NET_SSH1_RESPONSE_DATA]));
                            return $this->ssh->_string_shift($response[NET_SSH1_RESPONSE_DATA], $length);
                        case NET_SSH1_SMSG_STDERR_DATA:
                            break;
                        case NET_SSH1_SMSG_EXITSTATUS:
                            $this->ssh->_send_binary_packet(chr(NET_SSH1_CMSG_EXIT_CONFIRMATION));
                            fclose($this->ssh->fsock);
                            $this->ssh->bitmap = 0;
                            return false;
                        default:
                            user_error('Unknown packet received', E_USER_NOTICE);
                            return false;
                    }
                }
         }
    }

    /**
     * Closes the connection to an SSH server
     *
     * @access private
     */
    function _close()
    {
        switch ($this->mode) {
            case NET_SCP_SSH2:
                $this->ssh->_close_channel(NET_SSH2_CHANNEL_EXEC, true);
                break;
            case NET_SCP_SSH1:
                $this->ssh->disconnect();
         }
    }
}
