<?php

/**
 * Pure-PHP implementation of SFTP.
 *
 * PHP versions 4 and 5
 *
 * Currently only supports SFTPv2 and v3, which, according to wikipedia.org, "is the most widely used version,
 * implemented by the popular OpenSSH SFTP server".  If you want SFTPv4/5/6 support, provide me with access
 * to an SFTPv4/5/6 server.
 *
 * The API for this library is modeled after the API from PHP's {@link http://php.net/book.ftp FTP extension}.
 *
 * Here's a short example of how to use this library:
 * <code>
 * <?php
 *    include 'Net/SFTP.php';
 *
 *    $sftp = new Net_SFTP('www.domain.tld');
 *    if (!$sftp->login('username', 'password')) {
 *        exit('Login Failed');
 *    }
 *
 *    echo $sftp->pwd() . "\r\n";
 *    $sftp->put('filename.ext', 'hello, world!');
 *    print_r($sftp->nlist());
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
 * @package   Net_SFTP
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2009 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */

/**
 * Include Net_SSH2
 */
if (!class_exists('Net_SSH2')) {
    include_once 'SSH2.php';
}

/**#@+
 * @access public
 * @see Net_SFTP::getLog()
 */
/**
 * Returns the message numbers
 */
define('NET_SFTP_LOG_SIMPLE',  NET_SSH2_LOG_SIMPLE);
/**
 * Returns the message content
 */
define('NET_SFTP_LOG_COMPLEX', NET_SSH2_LOG_COMPLEX);
/**
 * Outputs the message content in real-time.
 */
define('NET_SFTP_LOG_REALTIME', 3);
/**#@-*/

/**
 * SFTP channel constant
 *
 * Net_SSH2::exec() uses 0 and Net_SSH2::read() / Net_SSH2::write() use 1.
 *
 * @see Net_SSH2::_send_channel_packet()
 * @see Net_SSH2::_get_channel_packet()
 * @access private
 */
define('NET_SFTP_CHANNEL', 0x100);

/**#@+
 * @access public
 * @see Net_SFTP::put()
 */
/**
 * Reads data from a local file.
 */
define('NET_SFTP_LOCAL_FILE',    1);
/**
 * Reads data from a string.
 */
// this value isn't really used anymore but i'm keeping it reserved for historical reasons
define('NET_SFTP_STRING',        2);
/**
 * Resumes an upload
 */
define('NET_SFTP_RESUME',        4);
/**
 * Append a local file to an already existing remote file
 */
define('NET_SFTP_RESUME_START',  8);
/**#@-*/

/**
 * Pure-PHP implementations of SFTP.
 *
 * @package Net_SFTP
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
class Net_SFTP extends Net_SSH2
{
    /**
     * Packet Types
     *
     * @see Net_SFTP::Net_SFTP()
     * @var Array
     * @access private
     */
    var $packet_types = array();

    /**
     * Status Codes
     *
     * @see Net_SFTP::Net_SFTP()
     * @var Array
     * @access private
     */
    var $status_codes = array();

    /**
     * The Request ID
     *
     * The request ID exists in the off chance that a packet is sent out-of-order.  Of course, this library doesn't support
     * concurrent actions, so it's somewhat academic, here.
     *
     * @var Integer
     * @see Net_SFTP::_send_sftp_packet()
     * @access private
     */
    var $request_id = false;

    /**
     * The Packet Type
     *
     * The request ID exists in the off chance that a packet is sent out-of-order.  Of course, this library doesn't support
     * concurrent actions, so it's somewhat academic, here.
     *
     * @var Integer
     * @see Net_SFTP::_get_sftp_packet()
     * @access private
     */
    var $packet_type = -1;

    /**
     * Packet Buffer
     *
     * @var String
     * @see Net_SFTP::_get_sftp_packet()
     * @access private
     */
    var $packet_buffer = '';

    /**
     * Extensions supported by the server
     *
     * @var Array
     * @see Net_SFTP::_initChannel()
     * @access private
     */
    var $extensions = array();

    /**
     * Server SFTP version
     *
     * @var Integer
     * @see Net_SFTP::_initChannel()
     * @access private
     */
    var $version;

    /**
     * Current working directory
     *
     * @var String
     * @see Net_SFTP::_realpath()
     * @see Net_SFTP::chdir()
     * @access private
     */
    var $pwd = false;

    /**
     * Packet Type Log
     *
     * @see Net_SFTP::getLog()
     * @var Array
     * @access private
     */
    var $packet_type_log = array();

    /**
     * Packet Log
     *
     * @see Net_SFTP::getLog()
     * @var Array
     * @access private
     */
    var $packet_log = array();

    /**
     * Error information
     *
     * @see Net_SFTP::getSFTPErrors()
     * @see Net_SFTP::getLastSFTPError()
     * @var String
     * @access private
     */
    var $sftp_errors = array();

    /**
     * Stat Cache
     *
     * Rather than always having to open a directory and close it immediately there after to see if a file is a directory
     * we'll cache the results.
     *
     * @see Net_SFTP::_update_stat_cache()
     * @see Net_SFTP::_remove_from_stat_cache()
     * @see Net_SFTP::_query_stat_cache()
     * @var Array
     * @access private
     */
    var $stat_cache = array();

    /**
     * Max SFTP Packet Size
     *
     * @see Net_SFTP::Net_SFTP()
     * @see Net_SFTP::get()
     * @var Array
     * @access private
     */
    var $max_sftp_packet;

    /**
     * Stat Cache Flag
     *
     * @see Net_SFTP::disableStatCache()
     * @see Net_SFTP::enableStatCache()
     * @var Boolean
     * @access private
     */
    var $use_stat_cache = true;

    /**
     * Sort Options
     *
     * @see Net_SFTP::_comparator()
     * @see Net_SFTP::setListOrder()
     * @var Array
     * @access private
     */
    var $sortOptions = array();

    /**
     * Default Constructor.
     *
     * Connects to an SFTP server
     *
     * @param String $host
     * @param optional Integer $port
     * @param optional Integer $timeout
     * @return Net_SFTP
     * @access public
     */
    function Net_SFTP($host, $port = 22, $timeout = 10)
    {
        parent::Net_SSH2($host, $port, $timeout);

        $this->max_sftp_packet = 1 << 15;

        $this->packet_types = array(
            1  => 'NET_SFTP_INIT',
            2  => 'NET_SFTP_VERSION',
            /* the format of SSH_FXP_OPEN changed between SFTPv4 and SFTPv5+:
                   SFTPv5+: http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-8.1.1
               pre-SFTPv5 : http://tools.ietf.org/html/draft-ietf-secsh-filexfer-04#section-6.3 */
            3  => 'NET_SFTP_OPEN',
            4  => 'NET_SFTP_CLOSE',
            5  => 'NET_SFTP_READ',
            6  => 'NET_SFTP_WRITE',
            7  => 'NET_SFTP_LSTAT',
            9  => 'NET_SFTP_SETSTAT',
            11 => 'NET_SFTP_OPENDIR',
            12 => 'NET_SFTP_READDIR',
            13 => 'NET_SFTP_REMOVE',
            14 => 'NET_SFTP_MKDIR',
            15 => 'NET_SFTP_RMDIR',
            16 => 'NET_SFTP_REALPATH',
            17 => 'NET_SFTP_STAT',
            /* the format of SSH_FXP_RENAME changed between SFTPv4 and SFTPv5+:
                   SFTPv5+: http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-8.3
               pre-SFTPv5 : http://tools.ietf.org/html/draft-ietf-secsh-filexfer-04#section-6.5 */
            18 => 'NET_SFTP_RENAME',
            19 => 'NET_SFTP_READLINK',
            20 => 'NET_SFTP_SYMLINK',

            101=> 'NET_SFTP_STATUS',
            102=> 'NET_SFTP_HANDLE',
            /* the format of SSH_FXP_NAME changed between SFTPv3 and SFTPv4+:
                   SFTPv4+: http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-9.4
               pre-SFTPv4 : http://tools.ietf.org/html/draft-ietf-secsh-filexfer-02#section-7 */
            103=> 'NET_SFTP_DATA',
            104=> 'NET_SFTP_NAME',
            105=> 'NET_SFTP_ATTRS',

            200=> 'NET_SFTP_EXTENDED'
        );
        $this->status_codes = array(
            0 => 'NET_SFTP_STATUS_OK',
            1 => 'NET_SFTP_STATUS_EOF',
            2 => 'NET_SFTP_STATUS_NO_SUCH_FILE',
            3 => 'NET_SFTP_STATUS_PERMISSION_DENIED',
            4 => 'NET_SFTP_STATUS_FAILURE',
            5 => 'NET_SFTP_STATUS_BAD_MESSAGE',
            6 => 'NET_SFTP_STATUS_NO_CONNECTION',
            7 => 'NET_SFTP_STATUS_CONNECTION_LOST',
            8 => 'NET_SFTP_STATUS_OP_UNSUPPORTED',
            9 => 'NET_SFTP_STATUS_INVALID_HANDLE',
            10 => 'NET_SFTP_STATUS_NO_SUCH_PATH',
            11 => 'NET_SFTP_STATUS_FILE_ALREADY_EXISTS',
            12 => 'NET_SFTP_STATUS_WRITE_PROTECT',
            13 => 'NET_SFTP_STATUS_NO_MEDIA',
            14 => 'NET_SFTP_STATUS_NO_SPACE_ON_FILESYSTEM',
            15 => 'NET_SFTP_STATUS_QUOTA_EXCEEDED',
            16 => 'NET_SFTP_STATUS_UNKNOWN_PRINCIPAL',
            17 => 'NET_SFTP_STATUS_LOCK_CONFLICT',
            18 => 'NET_SFTP_STATUS_DIR_NOT_EMPTY',
            19 => 'NET_SFTP_STATUS_NOT_A_DIRECTORY',
            20 => 'NET_SFTP_STATUS_INVALID_FILENAME',
            21 => 'NET_SFTP_STATUS_LINK_LOOP',
            22 => 'NET_SFTP_STATUS_CANNOT_DELETE',
            23 => 'NET_SFTP_STATUS_INVALID_PARAMETER',
            24 => 'NET_SFTP_STATUS_FILE_IS_A_DIRECTORY',
            25 => 'NET_SFTP_STATUS_BYTE_RANGE_LOCK_CONFLICT',
            26 => 'NET_SFTP_STATUS_BYTE_RANGE_LOCK_REFUSED',
            27 => 'NET_SFTP_STATUS_DELETE_PENDING',
            28 => 'NET_SFTP_STATUS_FILE_CORRUPT',
            29 => 'NET_SFTP_STATUS_OWNER_INVALID',
            30 => 'NET_SFTP_STATUS_GROUP_INVALID',
            31 => 'NET_SFTP_STATUS_NO_MATCHING_BYTE_RANGE_LOCK'
        );
        // http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-7.1
        // the order, in this case, matters quite a lot - see Net_SFTP::_parseAttributes() to understand why
        $this->attributes = array(
            0x00000001 => 'NET_SFTP_ATTR_SIZE',
            0x00000002 => 'NET_SFTP_ATTR_UIDGID', // defined in SFTPv3, removed in SFTPv4+
            0x00000004 => 'NET_SFTP_ATTR_PERMISSIONS',
            0x00000008 => 'NET_SFTP_ATTR_ACCESSTIME',
            // 0x80000000 will yield a floating point on 32-bit systems and converting floating points to integers
            // yields inconsistent behavior depending on how php is compiled.  so we left shift -1 (which, in
            // two's compliment, consists of all 1 bits) by 31.  on 64-bit systems this'll yield 0xFFFFFFFF80000000.
            // that's not a problem, however, and 'anded' and a 32-bit number, as all the leading 1 bits are ignored.
              -1 << 31 => 'NET_SFTP_ATTR_EXTENDED'
        );
        // http://tools.ietf.org/html/draft-ietf-secsh-filexfer-04#section-6.3
        // the flag definitions change somewhat in SFTPv5+.  if SFTPv5+ support is added to this library, maybe name
        // the array for that $this->open5_flags and similarily alter the constant names.
        $this->open_flags = array(
            0x00000001 => 'NET_SFTP_OPEN_READ',
            0x00000002 => 'NET_SFTP_OPEN_WRITE',
            0x00000004 => 'NET_SFTP_OPEN_APPEND',
            0x00000008 => 'NET_SFTP_OPEN_CREATE',
            0x00000010 => 'NET_SFTP_OPEN_TRUNCATE',
            0x00000020 => 'NET_SFTP_OPEN_EXCL'
        );
        // http://tools.ietf.org/html/draft-ietf-secsh-filexfer-04#section-5.2
        // see Net_SFTP::_parseLongname() for an explanation
        $this->file_types = array(
            1 => 'NET_SFTP_TYPE_REGULAR',
            2 => 'NET_SFTP_TYPE_DIRECTORY',
            3 => 'NET_SFTP_TYPE_SYMLINK',
            4 => 'NET_SFTP_TYPE_SPECIAL',
            5 => 'NET_SFTP_TYPE_UNKNOWN',
            // the followin types were first defined for use in SFTPv5+
            // http://tools.ietf.org/html/draft-ietf-secsh-filexfer-05#section-5.2
            6 => 'NET_SFTP_TYPE_SOCKET',
            7 => 'NET_SFTP_TYPE_CHAR_DEVICE',
            8 => 'NET_SFTP_TYPE_BLOCK_DEVICE',
            9 => 'NET_SFTP_TYPE_FIFO'
        );
        $this->_define_array(
            $this->packet_types,
            $this->status_codes,
            $this->attributes,
            $this->open_flags,
            $this->file_types
        );

        if (!defined('NET_SFTP_QUEUE_SIZE')) {
            define('NET_SFTP_QUEUE_SIZE', 50);
        }
    }

    /**
     * Login
     *
     * @param String $username
     * @param optional String $password
     * @return Boolean
     * @access public
     */
    function login($username)
    {
        $args = func_get_args();
        if (!call_user_func_array(array(&$this, '_login'), $args)) {
            return false;
        }

        $this->window_size_server_to_client[NET_SFTP_CHANNEL] = $this->window_size;

        $packet = pack('CNa*N3',
            NET_SSH2_MSG_CHANNEL_OPEN, strlen('session'), 'session', NET_SFTP_CHANNEL, $this->window_size, 0x4000);

        if (!$this->_send_binary_packet($packet)) {
            return false;
        }

        $this->channel_status[NET_SFTP_CHANNEL] = NET_SSH2_MSG_CHANNEL_OPEN;

        $response = $this->_get_channel_packet(NET_SFTP_CHANNEL);
        if ($response === false) {
            return false;
        }

        $packet = pack('CNNa*CNa*',
            NET_SSH2_MSG_CHANNEL_REQUEST, $this->server_channels[NET_SFTP_CHANNEL], strlen('subsystem'), 'subsystem', 1, strlen('sftp'), 'sftp');
        if (!$this->_send_binary_packet($packet)) {
            return false;
        }

        $this->channel_status[NET_SFTP_CHANNEL] = NET_SSH2_MSG_CHANNEL_REQUEST;

        $response = $this->_get_channel_packet(NET_SFTP_CHANNEL);
        if ($response === false) {
            // from PuTTY's psftp.exe
            $command = "test -x /usr/lib/sftp-server && exec /usr/lib/sftp-server\n" .
                       "test -x /usr/local/lib/sftp-server && exec /usr/local/lib/sftp-server\n" .
                       "exec sftp-server";
            // we don't do $this->exec($command, false) because exec() operates on a different channel and plus the SSH_MSG_CHANNEL_OPEN that exec() does
            // is redundant
            $packet = pack('CNNa*CNa*',
                NET_SSH2_MSG_CHANNEL_REQUEST, $this->server_channels[NET_SFTP_CHANNEL], strlen('exec'), 'exec', 1, strlen($command), $command);
            if (!$this->_send_binary_packet($packet)) {
                return false;
            }

            $this->channel_status[NET_SFTP_CHANNEL] = NET_SSH2_MSG_CHANNEL_REQUEST;

            $response = $this->_get_channel_packet(NET_SFTP_CHANNEL);
            if ($response === false) {
                return false;
            }
        }

        $this->channel_status[NET_SFTP_CHANNEL] = NET_SSH2_MSG_CHANNEL_DATA;

        if (!$this->_send_sftp_packet(NET_SFTP_INIT, "\0\0\0\3")) {
            return false;
        }

        $response = $this->_get_sftp_packet();
        if ($this->packet_type != NET_SFTP_VERSION) {
            user_error('Expected SSH_FXP_VERSION');
            return false;
        }

        extract(unpack('Nversion', $this->_string_shift($response, 4)));
        $this->version = $version;
        while (!empty($response)) {
            extract(unpack('Nlength', $this->_string_shift($response, 4)));
            $key = $this->_string_shift($response, $length);
            extract(unpack('Nlength', $this->_string_shift($response, 4)));
            $value = $this->_string_shift($response, $length);
            $this->extensions[$key] = $value;
        }

        /*
         SFTPv4+ defines a 'newline' extension.  SFTPv3 seems to have unofficial support for it via 'newline@vandyke.com',
         however, I'm not sure what 'newline@vandyke.com' is supposed to do (the fact that it's unofficial means that it's
         not in the official SFTPv3 specs) and 'newline@vandyke.com' / 'newline' are likely not drop-in substitutes for
         one another due to the fact that 'newline' comes with a SSH_FXF_TEXT bitmask whereas it seems unlikely that
         'newline@vandyke.com' would.
        */
        /*
        if (isset($this->extensions['newline@vandyke.com'])) {
            $this->extensions['newline'] = $this->extensions['newline@vandyke.com'];
            unset($this->extensions['newline@vandyke.com']);
        }
        */

        $this->request_id = 1;

        /*
         A Note on SFTPv4/5/6 support:
         <http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-5.1> states the following:

         "If the client wishes to interoperate with servers that support noncontiguous version
          numbers it SHOULD send '3'"

         Given that the server only sends its version number after the client has already done so, the above
         seems to be suggesting that v3 should be the default version.  This makes sense given that v3 is the
         most popular.

         <http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-5.5> states the following;

         "If the server did not send the "versions" extension, or the version-from-list was not included, the
          server MAY send a status response describing the failure, but MUST then close the channel without
          processing any further requests."

         So what do you do if you have a client whose initial SSH_FXP_INIT packet says it implements v3 and
         a server whose initial SSH_FXP_VERSION reply says it implements v4 and only v4?  If it only implements
         v4, the "versions" extension is likely not going to have been sent so version re-negotiation as discussed
         in draft-ietf-secsh-filexfer-13 would be quite impossible.  As such, what Net_SFTP would do is close the
         channel and reopen it with a new and updated SSH_FXP_INIT packet.
        */
        switch ($this->version) {
            case 2:
            case 3:
                break;
            default:
                return false;
        }

        $this->pwd = $this->_realpath('.');

        $this->_update_stat_cache($this->pwd, array());

        return true;
    }

    /**
     * Disable the stat cache
     *
     * @access public
     */
    function disableStatCache()
    {
        $this->use_stat_cache = false;
    }

    /**
     * Enable the stat cache
     *
     * @access public
     */
    function enableStatCache()
    {
        $this->use_stat_cache = true;
    }

    /**
     * Clear the stat cache
     *
     * @access public
     */
    function clearStatCache()
    {
        $this->stat_cache = array();
    }

    /**
     * Returns the current directory name
     *
     * @return Mixed
     * @access public
     */
    function pwd()
    {
        return $this->pwd;
    }

    /**
     * Logs errors
     *
     * @param String $response
     * @param optional Integer $status
     * @access public
     */
    function _logError($response, $status = -1)
    {
        if ($status == -1) {
            extract(unpack('Nstatus', $this->_string_shift($response, 4)));
        }

        $error = $this->status_codes[$status];

        if ($this->version > 2) {
            extract(unpack('Nlength', $this->_string_shift($response, 4)));
            $this->sftp_errors[] = $error . ': ' . $this->_string_shift($response, $length);
        } else {
            $this->sftp_errors[] = $error;
        }
    }

    /**
     * Canonicalize the Server-Side Path Name
     *
     * SFTP doesn't provide a mechanism by which the current working directory can be changed, so we'll emulate it.  Returns
     * the absolute (canonicalized) path.
     *
     * @see Net_SFTP::chdir()
     * @param String $path
     * @return Mixed
     * @access private
     */
    function _realpath($path)
    {
        if ($this->pwd === false) {
            // http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-8.9
            if (!$this->_send_sftp_packet(NET_SFTP_REALPATH, pack('Na*', strlen($path), $path))) {
                return false;
            }

            $response = $this->_get_sftp_packet();
            switch ($this->packet_type) {
                case NET_SFTP_NAME:
                    // although SSH_FXP_NAME is implemented differently in SFTPv3 than it is in SFTPv4+, the following
                    // should work on all SFTP versions since the only part of the SSH_FXP_NAME packet the following looks
                    // at is the first part and that part is defined the same in SFTP versions 3 through 6.
                    $this->_string_shift($response, 4); // skip over the count - it should be 1, anyway
                    extract(unpack('Nlength', $this->_string_shift($response, 4)));
                    return $this->_string_shift($response, $length);
                case NET_SFTP_STATUS:
                    $this->_logError($response);
                    return false;
                default:
                    user_error('Expected SSH_FXP_NAME or SSH_FXP_STATUS');
                    return false;
            }
        }

        if ($path[0] != '/') {
            $path = $this->pwd . '/' . $path;
        }

        $path = explode('/', $path);
        $new = array();
        foreach ($path as $dir) {
            if (!strlen($dir)) {
                continue;
            }
            switch ($dir) {
                case '..':
                    array_pop($new);
                case '.':
                    break;
                default:
                    $new[] = $dir;
            }
        }

        return '/' . implode('/', $new);
    }

    /**
     * Changes the current directory
     *
     * @param String $dir
     * @return Boolean
     * @access public
     */
    function chdir($dir)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        // assume current dir if $dir is empty
        if ($dir === '') {
            $dir = './';
        // suffix a slash if needed
        } elseif ($dir[strlen($dir) - 1] != '/') {
            $dir.= '/';
        }

        $dir = $this->_realpath($dir);

        // confirm that $dir is, in fact, a valid directory
        if ($this->use_stat_cache && is_array($this->_query_stat_cache($dir))) {
            $this->pwd = $dir;
            return true;
        }

        // we could do a stat on the alleged $dir to see if it's a directory but that doesn't tell us
        // the currently logged in user has the appropriate permissions or not. maybe you could see if
        // the file's uid / gid match the currently logged in user's uid / gid but how there's no easy
        // way to get those with SFTP

        if (!$this->_send_sftp_packet(NET_SFTP_OPENDIR, pack('Na*', strlen($dir), $dir))) {
            return false;
        }

        // see Net_SFTP::nlist() for a more thorough explanation of the following
        $response = $this->_get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_HANDLE:
                $handle = substr($response, 4);
                break;
            case NET_SFTP_STATUS:
                $this->_logError($response);
                return false;
            default:
                user_error('Expected SSH_FXP_HANDLE or SSH_FXP_STATUS');
                return false;
        }

        if (!$this->_close_handle($handle)) {
            return false;
        }

        $this->_update_stat_cache($dir, array());

        $this->pwd = $dir;
        return true;
    }

    /**
     * Returns a list of files in the given directory
     *
     * @param optional String $dir
     * @param optional Boolean $recursive
     * @return Mixed
     * @access public
     */
    function nlist($dir = '.', $recursive = false)
    {
        return $this->_nlist_helper($dir, $recursive, '');
    }

    /**
     * Helper method for nlist
     *
     * @param String $dir
     * @param Boolean $recursive
     * @param String $relativeDir
     * @return Mixed
     * @access private
     */
    function _nlist_helper($dir, $recursive, $relativeDir)
    {
        $files = $this->_list($dir, false);

        if (!$recursive) {
            return $files;
        }

        $result = array();
        foreach ($files as $value) {
            if ($value == '.' || $value == '..') {
                if ($relativeDir == '') {
                    $result[] = $value;
                }
                continue;
            }
            if (is_array($this->_query_stat_cache($this->_realpath($dir . '/' . $value)))) {
                $temp = $this->_nlist_helper($dir . '/' . $value, true, $relativeDir . $value . '/');
                $result = array_merge($result, $temp);
            } else {
                $result[] = $relativeDir . $value;
            }
        }

        return $result;
    }

    /**
     * Returns a detailed list of files in the given directory
     *
     * @param optional String $dir
     * @param optional Boolean $recursive
     * @return Mixed
     * @access public
     */
    function rawlist($dir = '.', $recursive = false)
    {
        $files = $this->_list($dir, true);
        if (!$recursive || $files === false) {
            return $files;
        }

        static $depth = 0;

        foreach ($files as $key=>$value) {
            if ($depth != 0 && $key == '..') {
                unset($files[$key]);
                continue;
            }
            if ($key != '.' && $key != '..' && is_array($this->_query_stat_cache($this->_realpath($dir . '/' . $key)))) {
                $depth++;
                $files[$key] = $this->rawlist($dir . '/' . $key, true);
                $depth--;
            } else {
                $files[$key] = (object) $value;
            }
        }

        return $files;
    }

    /**
     * Reads a list, be it detailed or not, of files in the given directory
     *
     * @param String $dir
     * @param optional Boolean $raw
     * @return Mixed
     * @access private
     */
    function _list($dir, $raw = true)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        $dir = $this->_realpath($dir . '/');
        if ($dir === false) {
            return false;
        }

        // http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-8.1.2
        if (!$this->_send_sftp_packet(NET_SFTP_OPENDIR, pack('Na*', strlen($dir), $dir))) {
            return false;
        }

        $response = $this->_get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_HANDLE:
                // http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-9.2
                // since 'handle' is the last field in the SSH_FXP_HANDLE packet, we'll just remove the first four bytes that
                // represent the length of the string and leave it at that
                $handle = substr($response, 4);
                break;
            case NET_SFTP_STATUS:
                // presumably SSH_FX_NO_SUCH_FILE or SSH_FX_PERMISSION_DENIED
                $this->_logError($response);
                return false;
            default:
                user_error('Expected SSH_FXP_HANDLE or SSH_FXP_STATUS');
                return false;
        }

        $this->_update_stat_cache($dir, array());

        $contents = array();
        while (true) {
            // http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-8.2.2
            // why multiple SSH_FXP_READDIR packets would be sent when the response to a single one can span arbitrarily many
            // SSH_MSG_CHANNEL_DATA messages is not known to me.
            if (!$this->_send_sftp_packet(NET_SFTP_READDIR, pack('Na*', strlen($handle), $handle))) {
                return false;
            }

            $response = $this->_get_sftp_packet();
            switch ($this->packet_type) {
                case NET_SFTP_NAME:
                    extract(unpack('Ncount', $this->_string_shift($response, 4)));
                    for ($i = 0; $i < $count; $i++) {
                        extract(unpack('Nlength', $this->_string_shift($response, 4)));
                        $shortname = $this->_string_shift($response, $length);
                        extract(unpack('Nlength', $this->_string_shift($response, 4)));
                        $longname = $this->_string_shift($response, $length);
                        $attributes = $this->_parseAttributes($response);
                        if (!isset($attributes['type'])) {
                            $fileType = $this->_parseLongname($longname);
                            if ($fileType) {
                                $attributes['type'] = $fileType;
                            }
                        }
                        $contents[$shortname] = $attributes + array('filename' => $shortname);

                        if (isset($attributes['type']) && $attributes['type'] == NET_SFTP_TYPE_DIRECTORY && ($shortname != '.' && $shortname != '..')) {
                            $this->_update_stat_cache($dir . '/' . $shortname, array());
                        } else {
                            if ($shortname == '..') {
                                $temp = $this->_realpath($dir . '/..') . '/.';
                            } else {
                                $temp = $dir . '/' . $shortname;
                            }
                            $this->_update_stat_cache($temp, (object) $attributes);
                        }
                        // SFTPv6 has an optional boolean end-of-list field, but we'll ignore that, since the
                        // final SSH_FXP_STATUS packet should tell us that, already.
                    }
                    break;
                case NET_SFTP_STATUS:
                    extract(unpack('Nstatus', $this->_string_shift($response, 4)));
                    if ($status != NET_SFTP_STATUS_EOF) {
                        $this->_logError($response, $status);
                        return false;
                    }
                    break 2;
                default:
                    user_error('Expected SSH_FXP_NAME or SSH_FXP_STATUS');
                    return false;
            }
        }

        if (!$this->_close_handle($handle)) {
            return false;
        }

        if (count($this->sortOptions)) {
            uasort($contents, array(&$this, '_comparator'));
        }

        return $raw ? $contents : array_keys($contents);
    }

    /**
     * Compares two rawlist entries using parameters set by setListOrder()
     *
     * Intended for use with uasort()
     *
     * @param Array $a
     * @param Array $b
     * @return Integer
     * @access private
     */
    function _comparator($a, $b)
    {
        switch (true) {
            case $a['filename'] === '.' || $b['filename'] === '.':
                if ($a['filename'] === $b['filename']) {
                    return 0;
                }
                return $a['filename'] === '.' ? -1 : 1;
            case $a['filename'] === '..' || $b['filename'] === '..':
                if ($a['filename'] === $b['filename']) {
                    return 0;
                }
                return $a['filename'] === '..' ? -1 : 1;
            case isset($a['type']) && $a['type'] === NET_SFTP_TYPE_DIRECTORY:
                if (!isset($b['type'])) {
                    return 1;
                }
                if ($b['type'] !== $a['type']) {
                    return -1;
                }
                break;
            case isset($b['type']) && $b['type'] === NET_SFTP_TYPE_DIRECTORY:
                return 1;
        }
        foreach ($this->sortOptions as $sort => $order) {
            if (!isset($a[$sort]) || !isset($b[$sort])) {
                if (isset($a[$sort])) {
                    return -1;
                }
                if (isset($b[$sort])) {
                    return 1;
                }
                return 0;
            }
            switch ($sort) {
                case 'filename':
                    $result = strcasecmp($a['filename'], $b['filename']);
                    if ($result) {
                        return $order === SORT_DESC ? -$result : $result;
                    }
                    break;
                case 'permissions':
                case 'mode':
                    $a[$sort]&= 07777;
                    $b[$sort]&= 07777;
                default:
                    if ($a[$sort] === $b[$sort]) {
                        break;
                    }
                    return $order === SORT_ASC ? $a[$sort] - $b[$sort] : $b[$sort] - $a[$sort];
            }
        }
    }

    /**
     * Defines how nlist() and rawlist() will be sorted - if at all.
     *
     * If sorting is enabled directories and files will be sorted independently with
     * directories appearing before files in the resultant array that is returned.
     *
     * Any parameter returned by stat is a valid sort parameter for this function.
     * Filename comparisons are case insensitive.
     *
     * Examples:
     *
     * $sftp->setListOrder('filename', SORT_ASC);
     * $sftp->setListOrder('size', SORT_DESC, 'filename', SORT_ASC);
     * $sftp->setListOrder(true);
     *    Separates directories from files but doesn't do any sorting beyond that
     * $sftp->setListOrder();
     *    Don't do any sort of sorting
     *
     * @access public
     */
    function setListOrder()
    {
        $this->sortOptions = array();
        $args = func_get_args();
        if (empty($args)) {
            return;
        }
        $len = count($args) & 0x7FFFFFFE;
        for ($i = 0; $i < $len; $i+=2) {
            $this->sortOptions[$args[$i]] = $args[$i + 1];
        }
        if (!count($this->sortOptions)) {
            $this->sortOptions = array('bogus' => true);
        }
    }

    /**
     * Returns the file size, in bytes, or false, on failure
     *
     * Files larger than 4GB will show up as being exactly 4GB.
     *
     * @param String $filename
     * @return Mixed
     * @access public
     */
    function size($filename)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        $result = $this->stat($filename);
        if ($result === false) {
            return false;
        }
        return isset($result['size']) ? $result['size'] : -1;
    }

    /**
     * Save files / directories to cache
     *
     * @param String $path
     * @param Mixed $value
     * @access private
     */
    function _update_stat_cache($path, $value)
    {
        // preg_replace('#^/|/(?=/)|/$#', '', $dir) == str_replace('//', '/', trim($path, '/'))
        $dirs = explode('/', preg_replace('#^/|/(?=/)|/$#', '', $path));

        $temp = &$this->stat_cache;
        $max = count($dirs) - 1;
        foreach ($dirs as $i=>$dir) {
            if (!isset($temp[$dir])) {
                $temp[$dir] = array();
            }
            if ($i === $max) {
                $temp[$dir] = $value;
                break;
            }
            $temp = &$temp[$dir];
        }
    }

    /**
     * Remove files / directories from cache
     *
     * @param String $path
     * @return Boolean
     * @access private
     */
    function _remove_from_stat_cache($path)
    {
        $dirs = explode('/', preg_replace('#^/|/(?=/)|/$#', '', $path));

        $temp = &$this->stat_cache;
        $max = count($dirs) - 1;
        foreach ($dirs as $i=>$dir) {
            if ($i === $max) {
                unset($temp[$dir]);
                return true;
            }
            if (!isset($temp[$dir])) {
                return false;
            }
            $temp = &$temp[$dir];
        }
    }

    /**
     * Checks cache for path
     *
     * Mainly used by file_exists
     *
     * @param String $dir
     * @return Mixed
     * @access private
     */
    function _query_stat_cache($path)
    {
        $dirs = explode('/', preg_replace('#^/|/(?=/)|/$#', '', $path));

        $temp = &$this->stat_cache;
        foreach ($dirs as $dir) {
            if (!isset($temp[$dir])) {
                return null;
            }
            $temp = &$temp[$dir];
        }
        return $temp;
    }

    /**
     * Returns general information about a file.
     *
     * Returns an array on success and false otherwise.
     *
     * @param String $filename
     * @return Mixed
     * @access public
     */
    function stat($filename)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        $filename = $this->_realpath($filename);
        if ($filename === false) {
            return false;
        }

        if ($this->use_stat_cache) {
            $result = $this->_query_stat_cache($filename);
            if (is_array($result) && isset($result['.'])) {
                return (array) $result['.'];
            }
            if (is_object($result)) {
                return (array) $result;
            }
        }

        $stat = $this->_stat($filename, NET_SFTP_STAT);
        if ($stat === false) {
            $this->_remove_from_stat_cache($filename);
            return false;
        }
        if (isset($stat['type'])) {
            if ($stat['type'] == NET_SFTP_TYPE_DIRECTORY) {
                $filename.= '/.';
            }
            $this->_update_stat_cache($filename, (object) $stat);
            return $stat;
        }

        $pwd = $this->pwd;
        $stat['type'] = $this->chdir($filename) ?
            NET_SFTP_TYPE_DIRECTORY :
            NET_SFTP_TYPE_REGULAR;
        $this->pwd = $pwd;

        if ($stat['type'] == NET_SFTP_TYPE_DIRECTORY) {
            $filename.= '/.';
        }
        $this->_update_stat_cache($filename, (object) $stat);

        return $stat;
    }

    /**
     * Returns general information about a file or symbolic link.
     *
     * Returns an array on success and false otherwise.
     *
     * @param String $filename
     * @return Mixed
     * @access public
     */
    function lstat($filename)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        $filename = $this->_realpath($filename);
        if ($filename === false) {
            return false;
        }

        if ($this->use_stat_cache) {
            $result = $this->_query_stat_cache($filename);
            if (is_array($result) && isset($result['.'])) {
                return (array) $result['.'];
            }
            if (is_object($result)) {
                return (array) $result;
            }
        }

        $lstat = $this->_stat($filename, NET_SFTP_LSTAT);
        if ($lstat === false) {
            $this->_remove_from_stat_cache($filename);
            return false;
        }
        if (isset($lstat['type'])) {
            if ($lstat['type'] == NET_SFTP_TYPE_DIRECTORY) {
                $filename.= '/.';
            }
            $this->_update_stat_cache($filename, (object) $lstat);
            return $lstat;
        }

        $stat = $this->_stat($filename, NET_SFTP_STAT);

        if ($lstat != $stat) {
            $lstat = array_merge($lstat, array('type' => NET_SFTP_TYPE_SYMLINK));
            $this->_update_stat_cache($filename, (object) $lstat);
            return $stat;
        }

        $pwd = $this->pwd;
        $lstat['type'] = $this->chdir($filename) ?
            NET_SFTP_TYPE_DIRECTORY :
            NET_SFTP_TYPE_REGULAR;
        $this->pwd = $pwd;

        if ($lstat['type'] == NET_SFTP_TYPE_DIRECTORY) {
            $filename.= '/.';
        }
        $this->_update_stat_cache($filename, (object) $lstat);

        return $lstat;
    }

    /**
     * Returns general information about a file or symbolic link
     *
     * Determines information without calling Net_SFTP::_realpath().
     * The second parameter can be either NET_SFTP_STAT or NET_SFTP_LSTAT.
     *
     * @param String $filename
     * @param Integer $type
     * @return Mixed
     * @access private
     */
    function _stat($filename, $type)
    {
        // SFTPv4+ adds an additional 32-bit integer field - flags - to the following:
        $packet = pack('Na*', strlen($filename), $filename);
        if (!$this->_send_sftp_packet($type, $packet)) {
            return false;
        }

        $response = $this->_get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_ATTRS:
                return $this->_parseAttributes($response);
            case NET_SFTP_STATUS:
                $this->_logError($response);
                return false;
        }

        user_error('Expected SSH_FXP_ATTRS or SSH_FXP_STATUS');
        return false;
    }

    /**
     * Truncates a file to a given length
     *
     * @param String $filename
     * @param Integer $new_size
     * @return Boolean
     * @access public
     */
    function truncate($filename, $new_size)
    {
        $attr = pack('N3', NET_SFTP_ATTR_SIZE, $new_size / 4294967296, $new_size); // 4294967296 == 0x100000000 == 1<<32

        return $this->_setstat($filename, $attr, false);
    }

    /**
     * Sets access and modification time of file.
     *
     * If the file does not exist, it will be created.
     *
     * @param String $filename
     * @param optional Integer $time
     * @param optional Integer $atime
     * @return Boolean
     * @access public
     */
    function touch($filename, $time = null, $atime = null)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        $filename = $this->_realpath($filename);
        if ($filename === false) {
            return false;
        }

        if (!isset($time)) {
            $time = time();
        }
        if (!isset($atime)) {
            $atime = $time;
        }

        $flags = NET_SFTP_OPEN_WRITE | NET_SFTP_OPEN_CREATE | NET_SFTP_OPEN_EXCL;
        $attr = pack('N3', NET_SFTP_ATTR_ACCESSTIME, $time, $atime);
        $packet = pack('Na*Na*', strlen($filename), $filename, $flags, $attr);
        if (!$this->_send_sftp_packet(NET_SFTP_OPEN, $packet)) {
            return false;
        }

        $response = $this->_get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_HANDLE:
                return $this->_close_handle(substr($response, 4));
            case NET_SFTP_STATUS:
                $this->_logError($response);
                break;
            default:
                user_error('Expected SSH_FXP_HANDLE or SSH_FXP_STATUS');
                return false;
        }

        return $this->_setstat($filename, $attr, false);
    }

    /**
     * Changes file or directory owner
     *
     * Returns true on success or false on error.
     *
     * @param String $filename
     * @param Integer $uid
     * @param optional Boolean $recursive
     * @return Boolean
     * @access public
     */
    function chown($filename, $uid, $recursive = false)
    {
        // quoting from <http://www.kernel.org/doc/man-pages/online/pages/man2/chown.2.html>,
        // "if the owner or group is specified as -1, then that ID is not changed"
        $attr = pack('N3', NET_SFTP_ATTR_UIDGID, $uid, -1);

        return $this->_setstat($filename, $attr, $recursive);
    }

    /**
     * Changes file or directory group
     *
     * Returns true on success or false on error.
     *
     * @param String $filename
     * @param Integer $gid
     * @param optional Boolean $recursive
     * @return Boolean
     * @access public
     */
    function chgrp($filename, $gid, $recursive = false)
    {
        $attr = pack('N3', NET_SFTP_ATTR_UIDGID, -1, $gid);

        return $this->_setstat($filename, $attr, $recursive);
    }

    /**
     * Set permissions on a file.
     *
     * Returns the new file permissions on success or false on error.
     * If $recursive is true than this just returns true or false.
     *
     * @param Integer $mode
     * @param String $filename
     * @param optional Boolean $recursive
     * @return Mixed
     * @access public
     */
    function chmod($mode, $filename, $recursive = false)
    {
        if (is_string($mode) && is_int($filename)) {
            $temp = $mode;
            $mode = $filename;
            $filename = $temp;
        }

        $attr = pack('N2', NET_SFTP_ATTR_PERMISSIONS, $mode & 07777);
        if (!$this->_setstat($filename, $attr, $recursive)) {
            return false;
        }
        if ($recursive) {
            return true;
        }

        // rather than return what the permissions *should* be, we'll return what they actually are.  this will also
        // tell us if the file actually exists.
        // incidentally, SFTPv4+ adds an additional 32-bit integer field - flags - to the following:
        $packet = pack('Na*', strlen($filename), $filename);
        if (!$this->_send_sftp_packet(NET_SFTP_STAT, $packet)) {
            return false;
        }

        $response = $this->_get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_ATTRS:
                $attrs = $this->_parseAttributes($response);
                return $attrs['permissions'];
            case NET_SFTP_STATUS:
                $this->_logError($response);
                return false;
        }

        user_error('Expected SSH_FXP_ATTRS or SSH_FXP_STATUS');
        return false;
    }

    /**
     * Sets information about a file
     *
     * @param String $filename
     * @param String $attr
     * @param Boolean $recursive
     * @return Boolean
     * @access private
     */
    function _setstat($filename, $attr, $recursive)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        $filename = $this->_realpath($filename);
        if ($filename === false) {
            return false;
        }

        $this->_remove_from_stat_cache($filename);

        if ($recursive) {
            $i = 0;
            $result = $this->_setstat_recursive($filename, $attr, $i);
            $this->_read_put_responses($i);
            return $result;
        }

        // SFTPv4+ has an additional byte field - type - that would need to be sent, as well. setting it to
        // SSH_FILEXFER_TYPE_UNKNOWN might work. if not, we'd have to do an SSH_FXP_STAT before doing an SSH_FXP_SETSTAT.
        if (!$this->_send_sftp_packet(NET_SFTP_SETSTAT, pack('Na*a*', strlen($filename), $filename, $attr))) {
            return false;
        }

        /*
         "Because some systems must use separate system calls to set various attributes, it is possible that a failure
          response will be returned, but yet some of the attributes may be have been successfully modified.  If possible,
          servers SHOULD avoid this situation; however, clients MUST be aware that this is possible."

          -- http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-8.6
        */
        $response = $this->_get_sftp_packet();
        if ($this->packet_type != NET_SFTP_STATUS) {
            user_error('Expected SSH_FXP_STATUS');
            return false;
        }

        extract(unpack('Nstatus', $this->_string_shift($response, 4)));
        if ($status != NET_SFTP_STATUS_OK) {
            $this->_logError($response, $status);
            return false;
        }

        return true;
    }

    /**
     * Recursively sets information on directories on the SFTP server
     *
     * Minimizes directory lookups and SSH_FXP_STATUS requests for speed.
     *
     * @param String $path
     * @param String $attr
     * @param Integer $i
     * @return Boolean
     * @access private
     */
    function _setstat_recursive($path, $attr, &$i)
    {
        if (!$this->_read_put_responses($i)) {
            return false;
        }
        $i = 0;
        $entries = $this->_list($path, true);

        if ($entries === false) {
            return $this->_setstat($path, $attr, false);
        }

        // normally $entries would have at least . and .. but it might not if the directories
        // permissions didn't allow reading
        if (empty($entries)) {
            return false;
        }

        unset($entries['.'], $entries['..']);
        foreach ($entries as $filename=>$props) {
            if (!isset($props['type'])) {
                return false;
            }

            $temp = $path . '/' . $filename;
            if ($props['type'] == NET_SFTP_TYPE_DIRECTORY) {
                if (!$this->_setstat_recursive($temp, $attr, $i)) {
                    return false;
                }
            } else {
                if (!$this->_send_sftp_packet(NET_SFTP_SETSTAT, pack('Na*a*', strlen($temp), $temp, $attr))) {
                    return false;
                }

                $i++;

                if ($i >= NET_SFTP_QUEUE_SIZE) {
                    if (!$this->_read_put_responses($i)) {
                        return false;
                    }
                    $i = 0;
                }
            }
        }

        if (!$this->_send_sftp_packet(NET_SFTP_SETSTAT, pack('Na*a*', strlen($path), $path, $attr))) {
            return false;
        }

        $i++;

        if ($i >= NET_SFTP_QUEUE_SIZE) {
            if (!$this->_read_put_responses($i)) {
                return false;
            }
            $i = 0;
        }

        return true;
    }

    /**
     * Return the target of a symbolic link
     *
     * @param String $link
     * @return Mixed
     * @access public
     */
    function readlink($link)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        $link = $this->_realpath($link);

        if (!$this->_send_sftp_packet(NET_SFTP_READLINK, pack('Na*', strlen($link), $link))) {
            return false;
        }

        $response = $this->_get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_NAME:
                break;
            case NET_SFTP_STATUS:
                $this->_logError($response);
                return false;
            default:
                user_error('Expected SSH_FXP_NAME or SSH_FXP_STATUS');
                return false;
        }

        extract(unpack('Ncount', $this->_string_shift($response, 4)));
        // the file isn't a symlink
        if (!$count) {
            return false;
        }

        extract(unpack('Nlength', $this->_string_shift($response, 4)));
        return $this->_string_shift($response, $length);
    }

    /**
     * Create a symlink
     *
     * symlink() creates a symbolic link to the existing target with the specified name link.
     *
     * @param String $target
     * @param String $link
     * @return Boolean
     * @access public
     */
    function symlink($target, $link)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        $target = $this->_realpath($target);
        $link = $this->_realpath($link);

        $packet = pack('Na*Na*', strlen($target), $target, strlen($link), $link);
        if (!$this->_send_sftp_packet(NET_SFTP_SYMLINK, $packet)) {
            return false;
        }

        $response = $this->_get_sftp_packet();
        if ($this->packet_type != NET_SFTP_STATUS) {
            user_error('Expected SSH_FXP_STATUS');
            return false;
        }

        extract(unpack('Nstatus', $this->_string_shift($response, 4)));
        if ($status != NET_SFTP_STATUS_OK) {
            $this->_logError($response, $status);
            return false;
        }

        return true;
    }

    /**
     * Creates a directory.
     *
     * @param String $dir
     * @return Boolean
     * @access public
     */
    function mkdir($dir, $mode = -1, $recursive = false)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        $dir = $this->_realpath($dir);
        // by not providing any permissions, hopefully the server will use the logged in users umask - their
        // default permissions.
        $attr = $mode == -1 ? "\0\0\0\0" : pack('N2', NET_SFTP_ATTR_PERMISSIONS, $mode & 07777);

        if ($recursive) {
            $dirs = explode('/', preg_replace('#/(?=/)|/$#', '', $dir));
            if (empty($dirs[0])) {
                array_shift($dirs);
                $dirs[0] = '/' . $dirs[0];
            }
            for ($i = 0; $i < count($dirs); $i++) {
                $temp = array_slice($dirs, 0, $i + 1);
                $temp = implode('/', $temp);
                $result = $this->_mkdir_helper($temp, $attr);
            }
            return $result;
        }

        return $this->_mkdir_helper($dir, $attr);
    }

    /**
     * Helper function for directory creation
     *
     * @param String $dir
     * @return Boolean
     * @access private
     */
    function _mkdir_helper($dir, $attr)
    {
        if (!$this->_send_sftp_packet(NET_SFTP_MKDIR, pack('Na*a*', strlen($dir), $dir, $attr))) {
            return false;
        }

        $response = $this->_get_sftp_packet();
        if ($this->packet_type != NET_SFTP_STATUS) {
            user_error('Expected SSH_FXP_STATUS');
            return false;
        }

        extract(unpack('Nstatus', $this->_string_shift($response, 4)));
        if ($status != NET_SFTP_STATUS_OK) {
            $this->_logError($response, $status);
            return false;
        }

        return true;
    }

    /**
     * Removes a directory.
     *
     * @param String $dir
     * @return Boolean
     * @access public
     */
    function rmdir($dir)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        $dir = $this->_realpath($dir);
        if ($dir === false) {
            return false;
        }

        if (!$this->_send_sftp_packet(NET_SFTP_RMDIR, pack('Na*', strlen($dir), $dir))) {
            return false;
        }

        $response = $this->_get_sftp_packet();
        if ($this->packet_type != NET_SFTP_STATUS) {
            user_error('Expected SSH_FXP_STATUS');
            return false;
        }

        extract(unpack('Nstatus', $this->_string_shift($response, 4)));
        if ($status != NET_SFTP_STATUS_OK) {
            // presumably SSH_FX_NO_SUCH_FILE or SSH_FX_PERMISSION_DENIED?
            $this->_logError($response, $status);
            return false;
        }

        $this->_remove_from_stat_cache($dir);
        // the following will do a soft delete, which would be useful if you deleted a file
        // and then tried to do a stat on the deleted file. the above, in contrast, does
        // a hard delete
        //$this->_update_stat_cache($dir, false);

        return true;
    }

    /**
     * Uploads a file to the SFTP server.
     *
     * By default, Net_SFTP::put() does not read from the local filesystem.  $data is dumped directly into $remote_file.
     * So, for example, if you set $data to 'filename.ext' and then do Net_SFTP::get(), you will get a file, twelve bytes
     * long, containing 'filename.ext' as its contents.
     *
     * Setting $mode to NET_SFTP_LOCAL_FILE will change the above behavior.  With NET_SFTP_LOCAL_FILE, $remote_file will
     * contain as many bytes as filename.ext does on your local filesystem.  If your filename.ext is 1MB then that is how
     * large $remote_file will be, as well.
     *
     * If $data is a resource then it'll be used as a resource instead.
     *
     * Currently, only binary mode is supported.  As such, if the line endings need to be adjusted, you will need to take
     * care of that, yourself.
     *
     * $mode can take an additional two parameters - NET_SFTP_RESUME and NET_SFTP_RESUME_START. These are bitwise AND'd with
     * $mode. So if you want to resume upload of a 300mb file on the local file system you'd set $mode to the following:
     *
     * NET_SFTP_LOCAL_FILE | NET_SFTP_RESUME
     *
     * If you wanted to simply append the full contents of a local file to the full contents of a remote file you'd replace
     * NET_SFTP_RESUME with NET_SFTP_RESUME_START.
     *
     * If $mode & (NET_SFTP_RESUME | NET_SFTP_RESUME_START) then NET_SFTP_RESUME_START will be assumed.
     *
     * $start and $local_start give you more fine grained control over this process and take precident over NET_SFTP_RESUME
     * when they're non-negative. ie. $start could let you write at the end of a file (like NET_SFTP_RESUME) or in the middle
     * of one. $local_start could let you start your reading from the end of a file (like NET_SFTP_RESUME_START) or in the
     * middle of one.
     *
     * Setting $local_start to > 0 or $mode | NET_SFTP_RESUME_START doesn't do anything unless $mode | NET_SFTP_LOCAL_FILE.
     *
     * @param String $remote_file
     * @param String|resource $data
     * @param optional Integer $mode
     * @param optional Integer $start
     * @param optional Integer $local_start
     * @return Boolean
     * @access public
     * @internal ASCII mode for SFTPv4/5/6 can be supported by adding a new function - Net_SFTP::setMode().
     */
    function put($remote_file, $data, $mode = NET_SFTP_STRING, $start = -1, $local_start = -1)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        $remote_file = $this->_realpath($remote_file);
        if ($remote_file === false) {
            return false;
        }

        $this->_remove_from_stat_cache($remote_file);

        $flags = NET_SFTP_OPEN_WRITE | NET_SFTP_OPEN_CREATE;
        // according to the SFTP specs, NET_SFTP_OPEN_APPEND should "force all writes to append data at the end of the file."
        // in practice, it doesn't seem to do that.
        //$flags|= ($mode & NET_SFTP_RESUME) ? NET_SFTP_OPEN_APPEND : NET_SFTP_OPEN_TRUNCATE;

        if ($start >= 0) {
            $offset = $start;
        } elseif ($mode & NET_SFTP_RESUME) {
            // if NET_SFTP_OPEN_APPEND worked as it should _size() wouldn't need to be called
            $size = $this->size($remote_file);
            $offset = $size !== false ? $size : 0;
        } else {
            $offset = 0;
            $flags|= NET_SFTP_OPEN_TRUNCATE;
        }

        $packet = pack('Na*N2', strlen($remote_file), $remote_file, $flags, 0);
        if (!$this->_send_sftp_packet(NET_SFTP_OPEN, $packet)) {
            return false;
        }

        $response = $this->_get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_HANDLE:
                $handle = substr($response, 4);
                break;
            case NET_SFTP_STATUS:
                $this->_logError($response);
                return false;
            default:
                user_error('Expected SSH_FXP_HANDLE or SSH_FXP_STATUS');
                return false;
        }

        // http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-8.2.3
        switch (true) {
            case is_resource($data):
                $mode = $mode & ~NET_SFTP_LOCAL_FILE;
                $fp = $data;
                break;
            case $mode & NET_SFTP_LOCAL_FILE:
                if (!is_file($data)) {
                    user_error("$data is not a valid file");
                    return false;
                }
                $fp = @fopen($data, 'rb');
                if (!$fp) {
                    return false;
                }
        }

        if (isset($fp)) {
            $stat = fstat($fp);
            $size = $stat['size'];

            if ($local_start >= 0) {
                fseek($fp, $local_start);
            } elseif ($mode & NET_SFTP_RESUME_START) {
                // do nothing
            } else {
                fseek($fp, $offset);
            }
        } else {
            $size = strlen($data);
        }

        $sent = 0;
        $size = $size < 0 ? ($size & 0x7FFFFFFF) + 0x80000000 : $size;

        $sftp_packet_size = 4096; // PuTTY uses 4096
        // make the SFTP packet be exactly 4096 bytes by including the bytes in the NET_SFTP_WRITE packets "header"
        $sftp_packet_size-= strlen($handle) + 25;
        $i = 0;
        while ($sent < $size) {
            $temp = isset($fp) ? fread($fp, $sftp_packet_size) : substr($data, $sent, $sftp_packet_size);
            $subtemp = $offset + $sent;
            $packet = pack('Na*N3a*', strlen($handle), $handle, $subtemp / 4294967296, $subtemp, strlen($temp), $temp);
            if (!$this->_send_sftp_packet(NET_SFTP_WRITE, $packet)) {
                if ($mode & NET_SFTP_LOCAL_FILE) {
                    fclose($fp);
                }
                return false;
            }
            $sent+= strlen($temp);

            $i++;

            if ($i == NET_SFTP_QUEUE_SIZE) {
                if (!$this->_read_put_responses($i)) {
                    $i = 0;
                    break;
                }
                $i = 0;
            }
        }

        if (!$this->_read_put_responses($i)) {
            if ($mode & NET_SFTP_LOCAL_FILE) {
                fclose($fp);
            }
            $this->_close_handle($handle);
            return false;
        }

        if ($mode & NET_SFTP_LOCAL_FILE) {
            fclose($fp);
        }

        return $this->_close_handle($handle);
    }

    /**
     * Reads multiple successive SSH_FXP_WRITE responses
     *
     * Sending an SSH_FXP_WRITE packet and immediately reading its response isn't as efficient as blindly sending out $i
     * SSH_FXP_WRITEs, in succession, and then reading $i responses.
     *
     * @param Integer $i
     * @return Boolean
     * @access private
     */
    function _read_put_responses($i)
    {
        while ($i--) {
            $response = $this->_get_sftp_packet();
            if ($this->packet_type != NET_SFTP_STATUS) {
                user_error('Expected SSH_FXP_STATUS');
                return false;
            }

            extract(unpack('Nstatus', $this->_string_shift($response, 4)));
            if ($status != NET_SFTP_STATUS_OK) {
                $this->_logError($response, $status);
                break;
            }
        }

        return $i < 0;
    }

    /**
     * Close handle
     *
     * @param String $handle
     * @return Boolean
     * @access private
     */
    function _close_handle($handle)
    {
        if (!$this->_send_sftp_packet(NET_SFTP_CLOSE, pack('Na*', strlen($handle), $handle))) {
            return false;
        }

        // "The client MUST release all resources associated with the handle regardless of the status."
        //  -- http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-8.1.3
        $response = $this->_get_sftp_packet();
        if ($this->packet_type != NET_SFTP_STATUS) {
            user_error('Expected SSH_FXP_STATUS');
            return false;
        }

        extract(unpack('Nstatus', $this->_string_shift($response, 4)));
        if ($status != NET_SFTP_STATUS_OK) {
            $this->_logError($response, $status);
            return false;
        }

        return true;
    }

    /**
     * Downloads a file from the SFTP server.
     *
     * Returns a string containing the contents of $remote_file if $local_file is left undefined or a boolean false if
     * the operation was unsuccessful.  If $local_file is defined, returns true or false depending on the success of the
     * operation.
     *
     * $offset and $length can be used to download files in chunks.
     *
     * @param String $remote_file
     * @param optional String $local_file
     * @param optional Integer $offset
     * @param optional Integer $length
     * @return Mixed
     * @access public
     */
    function get($remote_file, $local_file = false, $offset = 0, $length = -1)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        $remote_file = $this->_realpath($remote_file);
        if ($remote_file === false) {
            return false;
        }

        $packet = pack('Na*N2', strlen($remote_file), $remote_file, NET_SFTP_OPEN_READ, 0);
        if (!$this->_send_sftp_packet(NET_SFTP_OPEN, $packet)) {
            return false;
        }

        $response = $this->_get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_HANDLE:
                $handle = substr($response, 4);
                break;
            case NET_SFTP_STATUS: // presumably SSH_FX_NO_SUCH_FILE or SSH_FX_PERMISSION_DENIED
                $this->_logError($response);
                return false;
            default:
                user_error('Expected SSH_FXP_HANDLE or SSH_FXP_STATUS');
                return false;
        }

        if (is_resource($local_file)) {
            $fp = $local_file;
            $stat = fstat($fp);
            $res_offset = $stat['size'];
        } else {
            $res_offset = 0;
            if ($local_file !== false) {
                $fp = fopen($local_file, 'wb');
                if (!$fp) {
                    return false;
                }
            } else {
                $content = '';
            }
        }

        $fclose_check = $local_file !== false && !is_resource($local_file);

        $start = $offset;
        $size = $this->max_sftp_packet < $length || $length < 0 ? $this->max_sftp_packet : $length;
        while (true) {
            $packet = pack('Na*N3', strlen($handle), $handle, $offset / 4294967296, $offset, $size);
            if (!$this->_send_sftp_packet(NET_SFTP_READ, $packet)) {
                if ($fclose_check) {
                    fclose($fp);
                }
                return false;
            }

            $response = $this->_get_sftp_packet();
            switch ($this->packet_type) {
                case NET_SFTP_DATA:
                    $temp = substr($response, 4);
                    $offset+= strlen($temp);
                    if ($local_file === false) {
                        $content.= $temp;
                    } else {
                        fputs($fp, $temp);
                    }
                    break;
                case NET_SFTP_STATUS:
                    // could, in theory, return false if !strlen($content) but we'll hold off for the time being
                    $this->_logError($response);
                    break 2;
                default:
                    user_error('Expected SSH_FXP_DATA or SSH_FXP_STATUS');
                    if ($fclose_check) {
                        fclose($fp);
                    }
                    return false;
            }

            if ($length > 0 && $length <= $offset - $start) {
                break;
            }
        }

        if ($length > 0 && $length <= $offset - $start) {
            if ($local_file === false) {
                $content = substr($content, 0, $length);
            } else {
                ftruncate($fp, $length + $res_offset);
            }
        }

        if ($fclose_check) {
            fclose($fp);
        }

        if (!$this->_close_handle($handle)) {
            return false;
        }

        // if $content isn't set that means a file was written to
        return isset($content) ? $content : true;
    }

    /**
     * Deletes a file on the SFTP server.
     *
     * @param String $path
     * @param Boolean $recursive
     * @return Boolean
     * @access public
     */
    function delete($path, $recursive = true)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        $path = $this->_realpath($path);
        if ($path === false) {
            return false;
        }

        // http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-8.3
        if (!$this->_send_sftp_packet(NET_SFTP_REMOVE, pack('Na*', strlen($path), $path))) {
            return false;
        }

        $response = $this->_get_sftp_packet();
        if ($this->packet_type != NET_SFTP_STATUS) {
            user_error('Expected SSH_FXP_STATUS');
            return false;
        }

        // if $status isn't SSH_FX_OK it's probably SSH_FX_NO_SUCH_FILE or SSH_FX_PERMISSION_DENIED
        extract(unpack('Nstatus', $this->_string_shift($response, 4)));
        if ($status != NET_SFTP_STATUS_OK) {
            $this->_logError($response, $status);
            if (!$recursive) {
                return false;
            }
            $i = 0;
            $result = $this->_delete_recursive($path, $i);
            $this->_read_put_responses($i);
            return $result;
        }

        $this->_remove_from_stat_cache($path);

        return true;
    }

    /**
     * Recursively deletes directories on the SFTP server
     *
     * Minimizes directory lookups and SSH_FXP_STATUS requests for speed.
     *
     * @param String $path
     * @param Integer $i
     * @return Boolean
     * @access private
     */
    function _delete_recursive($path, &$i)
    {
        if (!$this->_read_put_responses($i)) {
            return false;
        }
        $i = 0;
        $entries = $this->_list($path, true);

        // normally $entries would have at least . and .. but it might not if the directories
        // permissions didn't allow reading
        if (empty($entries)) {
            return false;
        }

        unset($entries['.'], $entries['..']);
        foreach ($entries as $filename=>$props) {
            if (!isset($props['type'])) {
                return false;
            }

            $temp = $path . '/' . $filename;
            if ($props['type'] == NET_SFTP_TYPE_DIRECTORY) {
                if (!$this->_delete_recursive($temp, $i)) {
                    return false;
                }
            } else {
                if (!$this->_send_sftp_packet(NET_SFTP_REMOVE, pack('Na*', strlen($temp), $temp))) {
                    return false;
                }

                $i++;

                if ($i >= NET_SFTP_QUEUE_SIZE) {
                    if (!$this->_read_put_responses($i)) {
                        return false;
                    }
                    $i = 0;
                }
            }
            $this->_remove_from_stat_cache($path);
        }

        if (!$this->_send_sftp_packet(NET_SFTP_RMDIR, pack('Na*', strlen($path), $path))) {
            return false;
        }

        $i++;

        if ($i >= NET_SFTP_QUEUE_SIZE) {
            if (!$this->_read_put_responses($i)) {
                return false;
            }
            $i = 0;
        }

        return true;
    }

    /**
     * Checks whether a file or directory exists
     *
     * @param String $path
     * @return Boolean
     * @access public
     */
    function file_exists($path)
    {
        if ($this->use_stat_cache) {
            $path = $this->_realpath($path);

            $result = $this->_query_stat_cache($path);

            if (isset($result)) {
                // return true if $result is an array or if it's an stdClass object
                return $result !== false;
            }
        }

        return $this->stat($path) !== false;
    }

    /**
     * Tells whether the filename is a directory
     *
     * @param String $path
     * @return Boolean
     * @access public
     */
    function is_dir($path)
    {
        $result = $this->_get_stat_cache_prop($path, 'type');
        if ($result === false) {
            return false;
        }
        return $result === NET_SFTP_TYPE_DIRECTORY;
    }

    /**
     * Tells whether the filename is a regular file
     *
     * @param String $path
     * @return Boolean
     * @access public
     */
    function is_file($path)
    {
        $result = $this->_get_stat_cache_prop($path, 'type');
        if ($result === false) {
            return false;
        }
        return $result === NET_SFTP_TYPE_REGULAR;
    }

    /**
     * Tells whether the filename is a symbolic link
     *
     * @param String $path
     * @return Boolean
     * @access public
     */
    function is_link($path)
    {
        $result = $this->_get_stat_cache_prop($path, 'type');
        if ($result === false) {
            return false;
        }
        return $result === NET_SFTP_TYPE_SYMLINK;
    }

    /**
     * Gets last access time of file
     *
     * @param String $path
     * @return Mixed
     * @access public
     */
    function fileatime($path)
    {
        return $this->_get_stat_cache_prop($path, 'atime');
    }

    /**
     * Gets file modification time
     *
     * @param String $path
     * @return Mixed
     * @access public
     */
    function filemtime($path)
    {
        return $this->_get_stat_cache_prop($path, 'mtime');
    }

    /**
     * Gets file permissions
     *
     * @param String $path
     * @return Mixed
     * @access public
     */
    function fileperms($path)
    {
        return $this->_get_stat_cache_prop($path, 'permissions');
    }

    /**
     * Gets file owner
     *
     * @param String $path
     * @return Mixed
     * @access public
     */
    function fileowner($path)
    {
        return $this->_get_stat_cache_prop($path, 'uid');
    }

    /**
     * Gets file group
     *
     * @param String $path
     * @return Mixed
     * @access public
     */
    function filegroup($path)
    {
        return $this->_get_stat_cache_prop($path, 'gid');
    }

    /**
     * Gets file size
     *
     * @param String $path
     * @return Mixed
     * @access public
     */
    function filesize($path)
    {
        return $this->_get_stat_cache_prop($path, 'size');
    }

    /**
     * Gets file type
     *
     * @param String $path
     * @return Mixed
     * @access public
     */
    function filetype($path)
    {
        $type = $this->_get_stat_cache_prop($path, 'type');
        if ($type === false) {
            return false;
        }

        switch ($type) {
            case NET_SFTP_TYPE_BLOCK_DEVICE: return 'block';
            case NET_SFTP_TYPE_CHAR_DEVICE: return 'char';
            case NET_SFTP_TYPE_DIRECTORY: return 'dir';
            case NET_SFTP_TYPE_FIFO: return 'fifo';
            case NET_SFTP_TYPE_REGULAR: return 'file';
            case NET_SFTP_TYPE_SYMLINK: return 'link';
            default: return false;
        }
    }

    /**
     * Return a stat properity
     *
     * Uses cache if appropriate.
     *
     * @param String $path
     * @param String $prop
     * @return Mixed
     * @access private
     */
    function _get_stat_cache_prop($path, $prop)
    {
        if ($this->use_stat_cache) {
            $path = $this->_realpath($path);

            $result = $this->_query_stat_cache($path);

            if (is_object($result) && isset($result->$prop)) {
                return $result->$prop;
            }
        }

        $result = $this->stat($path);

        if ($result === false || !isset($result[$prop])) {
            return false;
        }

        return $result[$prop];
    }

    /**
     * Renames a file or a directory on the SFTP server
     *
     * @param String $oldname
     * @param String $newname
     * @return Boolean
     * @access public
     */
    function rename($oldname, $newname)
    {
        if (!($this->bitmap & NET_SSH2_MASK_LOGIN)) {
            return false;
        }

        $oldname = $this->_realpath($oldname);
        $newname = $this->_realpath($newname);
        if ($oldname === false || $newname === false) {
            return false;
        }

        // http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-8.3
        $packet = pack('Na*Na*', strlen($oldname), $oldname, strlen($newname), $newname);
        if (!$this->_send_sftp_packet(NET_SFTP_RENAME, $packet)) {
            return false;
        }

        $response = $this->_get_sftp_packet();
        if ($this->packet_type != NET_SFTP_STATUS) {
            user_error('Expected SSH_FXP_STATUS');
            return false;
        }

        // if $status isn't SSH_FX_OK it's probably SSH_FX_NO_SUCH_FILE or SSH_FX_PERMISSION_DENIED
        extract(unpack('Nstatus', $this->_string_shift($response, 4)));
        if ($status != NET_SFTP_STATUS_OK) {
            $this->_logError($response, $status);
            return false;
        }

        // don't move the stat cache entry over since this operation could very well change the
        // atime and mtime attributes
        //$this->_update_stat_cache($newname, $this->_query_stat_cache($oldname));
        $this->_remove_from_stat_cache($oldname);
        $this->_remove_from_stat_cache($newname);

        return true;
    }

    /**
     * Parse Attributes
     *
     * See '7.  File Attributes' of draft-ietf-secsh-filexfer-13 for more info.
     *
     * @param String $response
     * @return Array
     * @access private
     */
    function _parseAttributes(&$response)
    {
        $attr = array();
        extract(unpack('Nflags', $this->_string_shift($response, 4)));
        // SFTPv4+ have a type field (a byte) that follows the above flag field
        foreach ($this->attributes as $key => $value) {
            switch ($flags & $key) {
                case NET_SFTP_ATTR_SIZE: // 0x00000001
                    // The size attribute is defined as an unsigned 64-bit integer.
                    // The following will use floats on 32-bit platforms, if necessary.
                    // As can be seen in the BigInteger class, floats are generally
                    // IEEE 754 binary64 "double precision" on such platforms and
                    // as such can represent integers of at least 2^50 without loss
                    // of precision. Interpreted in filesize, 2^50 bytes = 1024 TiB.
                    $attr['size'] = hexdec(bin2hex($this->_string_shift($response, 8)));
                    break;
                case NET_SFTP_ATTR_UIDGID: // 0x00000002 (SFTPv3 only)
                    $attr+= unpack('Nuid/Ngid', $this->_string_shift($response, 8));
                    break;
                case NET_SFTP_ATTR_PERMISSIONS: // 0x00000004
                    $attr+= unpack('Npermissions', $this->_string_shift($response, 4));
                    // mode == permissions; permissions was the original array key and is retained for bc purposes.
                    // mode was added because that's the more industry standard terminology
                    $attr+= array('mode' => $attr['permissions']);
                    $fileType = $this->_parseMode($attr['permissions']);
                    if ($fileType !== false) {
                        $attr+= array('type' => $fileType);
                    }
                    break;
                case NET_SFTP_ATTR_ACCESSTIME: // 0x00000008
                    $attr+= unpack('Natime/Nmtime', $this->_string_shift($response, 8));
                    break;
                case NET_SFTP_ATTR_EXTENDED: // 0x80000000
                    extract(unpack('Ncount', $this->_string_shift($response, 4)));
                    for ($i = 0; $i < $count; $i++) {
                        extract(unpack('Nlength', $this->_string_shift($response, 4)));
                        $key = $this->_string_shift($response, $length);
                        extract(unpack('Nlength', $this->_string_shift($response, 4)));
                        $attr[$key] = $this->_string_shift($response, $length);
                    }
            }
        }
        return $attr;
    }

    /**
     * Attempt to identify the file type
     *
     * Quoting the SFTP RFC, "Implementations MUST NOT send bits that are not defined" but they seem to anyway
     *
     * @param Integer $mode
     * @return Integer
     * @access private
     */
    function _parseMode($mode)
    {
        // values come from http://lxr.free-electrons.com/source/include/uapi/linux/stat.h#L12
        // see, also, http://linux.die.net/man/2/stat
        switch ($mode & 0170000) {// ie. 1111 0000 0000 0000
            case 0000000: // no file type specified - figure out the file type using alternative means
                return false;
            case 0040000:
                return NET_SFTP_TYPE_DIRECTORY;
            case 0100000:
                return NET_SFTP_TYPE_REGULAR;
            case 0120000:
                return NET_SFTP_TYPE_SYMLINK;
            // new types introduced in SFTPv5+
            // http://tools.ietf.org/html/draft-ietf-secsh-filexfer-05#section-5.2
            case 0010000: // named pipe (fifo)
                return NET_SFTP_TYPE_FIFO;
            case 0020000: // character special
                return NET_SFTP_TYPE_CHAR_DEVICE;
            case 0060000: // block special
                return NET_SFTP_TYPE_BLOCK_DEVICE;
            case 0140000: // socket
                return NET_SFTP_TYPE_SOCKET;
            case 0160000: // whiteout
                // "SPECIAL should be used for files that are of
                //  a known type which cannot be expressed in the protocol"
                return NET_SFTP_TYPE_SPECIAL;
            default:
                return NET_SFTP_TYPE_UNKNOWN;
        }
    }

    /**
     * Parse Longname
     *
     * SFTPv3 doesn't provide any easy way of identifying a file type.  You could try to open
     * a file as a directory and see if an error is returned or you could try to parse the
     * SFTPv3-specific longname field of the SSH_FXP_NAME packet.  That's what this function does.
     * The result is returned using the
     * {@link http://tools.ietf.org/html/draft-ietf-secsh-filexfer-04#section-5.2 SFTPv4 type constants}.
     *
     * If the longname is in an unrecognized format bool(false) is returned.
     *
     * @param String $longname
     * @return Mixed
     * @access private
     */
    function _parseLongname($longname)
    {
        // http://en.wikipedia.org/wiki/Unix_file_types
        // http://en.wikipedia.org/wiki/Filesystem_permissions#Notation_of_traditional_Unix_permissions
        if (preg_match('#^[^/]([r-][w-][xstST-]){3}#', $longname)) {
            switch ($longname[0]) {
                case '-':
                    return NET_SFTP_TYPE_REGULAR;
                case 'd':
                    return NET_SFTP_TYPE_DIRECTORY;
                case 'l':
                    return NET_SFTP_TYPE_SYMLINK;
                default:
                    return NET_SFTP_TYPE_SPECIAL;
            }
        }

        return false;
    }

    /**
     * Sends SFTP Packets
     *
     * See '6. General Packet Format' of draft-ietf-secsh-filexfer-13 for more info.
     *
     * @param Integer $type
     * @param String $data
     * @see Net_SFTP::_get_sftp_packet()
     * @see Net_SSH2::_send_channel_packet()
     * @return Boolean
     * @access private
     */
    function _send_sftp_packet($type, $data)
    {
        $packet = $this->request_id !== false ?
            pack('NCNa*', strlen($data) + 5, $type, $this->request_id, $data) :
            pack('NCa*',  strlen($data) + 1, $type, $data);

        $start = strtok(microtime(), ' ') + strtok(''); // http://php.net/microtime#61838
        $result = $this->_send_channel_packet(NET_SFTP_CHANNEL, $packet);
        $stop = strtok(microtime(), ' ') + strtok('');

        if (defined('NET_SFTP_LOGGING')) {
            $packet_type = '-> ' . $this->packet_types[$type] .
                           ' (' . round($stop - $start, 4) . 's)';
            if (NET_SFTP_LOGGING == NET_SFTP_LOG_REALTIME) {
                echo "<pre>\r\n" . $this->_format_log(array($data), array($packet_type)) . "\r\n</pre>\r\n";
                flush();
                ob_flush();
            } else {
                $this->packet_type_log[] = $packet_type;
                if (NET_SFTP_LOGGING == NET_SFTP_LOG_COMPLEX) {
                    $this->packet_log[] = $data;
                }
            }
        }

        return $result;
    }

    /**
     * Receives SFTP Packets
     *
     * See '6. General Packet Format' of draft-ietf-secsh-filexfer-13 for more info.
     *
     * Incidentally, the number of SSH_MSG_CHANNEL_DATA messages has no bearing on the number of SFTP packets present.
     * There can be one SSH_MSG_CHANNEL_DATA messages containing two SFTP packets or there can be two SSH_MSG_CHANNEL_DATA
     * messages containing one SFTP packet.
     *
     * @see Net_SFTP::_send_sftp_packet()
     * @return String
     * @access private
     */
    function _get_sftp_packet()
    {
        $this->curTimeout = false;

        $start = strtok(microtime(), ' ') + strtok(''); // http://php.net/microtime#61838

        // SFTP packet length
        while (strlen($this->packet_buffer) < 4) {
            $temp = $this->_get_channel_packet(NET_SFTP_CHANNEL);
            if (is_bool($temp)) {
                $this->packet_type = false;
                $this->packet_buffer = '';
                return false;
            }
            $this->packet_buffer.= $temp;
        }
        extract(unpack('Nlength', $this->_string_shift($this->packet_buffer, 4)));
        $tempLength = $length;
        $tempLength-= strlen($this->packet_buffer);

        // SFTP packet type and data payload
        while ($tempLength > 0) {
            $temp = $this->_get_channel_packet(NET_SFTP_CHANNEL);
            if (is_bool($temp)) {
                $this->packet_type = false;
                $this->packet_buffer = '';
                return false;
            }
            $this->packet_buffer.= $temp;
            $tempLength-= strlen($temp);
        }

        $stop = strtok(microtime(), ' ') + strtok('');

        $this->packet_type = ord($this->_string_shift($this->packet_buffer));

        if ($this->request_id !== false) {
            $this->_string_shift($this->packet_buffer, 4); // remove the request id
            $length-= 5; // account for the request id and the packet type
        } else {
            $length-= 1; // account for the packet type
        }

        $packet = $this->_string_shift($this->packet_buffer, $length);

        if (defined('NET_SFTP_LOGGING')) {
            $packet_type = '<- ' . $this->packet_types[$this->packet_type] .
                           ' (' . round($stop - $start, 4) . 's)';
            if (NET_SFTP_LOGGING == NET_SFTP_LOG_REALTIME) {
                echo "<pre>\r\n" . $this->_format_log(array($packet), array($packet_type)) . "\r\n</pre>\r\n";
                flush();
                ob_flush();
            } else {
                $this->packet_type_log[] = $packet_type;
                if (NET_SFTP_LOGGING == NET_SFTP_LOG_COMPLEX) {
                    $this->packet_log[] = $packet;
                }
            }
        }

        return $packet;
    }

    /**
     * Returns a log of the packets that have been sent and received.
     *
     * Returns a string if NET_SFTP_LOGGING == NET_SFTP_LOG_COMPLEX, an array if NET_SFTP_LOGGING == NET_SFTP_LOG_SIMPLE and false if !defined('NET_SFTP_LOGGING')
     *
     * @access public
     * @return String or Array
     */
    function getSFTPLog()
    {
        if (!defined('NET_SFTP_LOGGING')) {
            return false;
        }

        switch (NET_SFTP_LOGGING) {
            case NET_SFTP_LOG_COMPLEX:
                return $this->_format_log($this->packet_log, $this->packet_type_log);
                break;
            //case NET_SFTP_LOG_SIMPLE:
            default:
                return $this->packet_type_log;
        }
    }

    /**
     * Returns all errors
     *
     * @return String
     * @access public
     */
    function getSFTPErrors()
    {
        return $this->sftp_errors;
    }

    /**
     * Returns the last error
     *
     * @return String
     * @access public
     */
    function getLastSFTPError()
    {
        return count($this->sftp_errors) ? $this->sftp_errors[count($this->sftp_errors) - 1] : '';
    }

    /**
     * Get supported SFTP versions
     *
     * @return Array
     * @access public
     */
    function getSupportedVersions()
    {
        $temp = array('version' => $this->version);
        if (isset($this->extensions['versions'])) {
            $temp['extensions'] = $this->extensions['versions'];
        }
        return $temp;
    }

    /**
     * Disconnect
     *
     * @param Integer $reason
     * @return Boolean
     * @access private
     */
    function _disconnect($reason)
    {
        $this->pwd = false;
        parent::_disconnect($reason);
    }
}
