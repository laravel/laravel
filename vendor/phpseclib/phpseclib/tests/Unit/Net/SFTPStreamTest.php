<?php
/**
 * @author    Andreas Fischer <bantu@phpbb.com>
 * @copyright 2014 Andreas Fischer
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

require_once 'Net/SFTP/Stream.php';

class Unit_Net_SFTPStreamTest extends PhpseclibTestCase
{
    protected $protocol = 'sftptest';

    public function setUp()
    {
        parent::setUp();
        if (in_array($this->protocol, stream_get_wrappers())) {
            stream_wrapper_unregister($this->protocol);
        }
    }

    public function testRegisterFromSideEffect()
    {
        // Including the file registers 'sftp' as a stream.
        $this->assertContains('sftp', stream_get_wrappers());
    }

    public function testRegisterWithArgument()
    {
        Net_SFTP_Stream::register($this->protocol);
        $this->assertContains($this->protocol, stream_get_wrappers());
    }
}
