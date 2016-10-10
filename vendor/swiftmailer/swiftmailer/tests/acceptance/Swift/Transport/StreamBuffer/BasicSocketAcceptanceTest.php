<?php

require_once __DIR__.'/AbstractStreamBufferAcceptanceTest.php';

class Swift_Transport_StreamBuffer_BasicSocketAcceptanceTest
    extends Swift_Transport_StreamBuffer_AbstractStreamBufferAcceptanceTest
{
    public function setUp()
    {
        if (!defined('SWIFT_SMTP_HOST')) {
            $this->markTestSkipped(
                'Cannot run test without an SMTP host to connect to (define '.
                'SWIFT_SMTP_HOST in tests/acceptance.conf.php if you wish to run this test)'
             );
        }
        parent::setUp();
    }

    protected function _initializeBuffer()
    {
        $parts = explode(':', SWIFT_SMTP_HOST);
        $host = $parts[0];
        $port = isset($parts[1]) ? $parts[1] : 25;

        $this->_buffer->initialize(array(
            'type' => Swift_Transport_IoBuffer::TYPE_SOCKET,
            'host' => $host,
            'port' => $port,
            'protocol' => 'tcp',
            'blocking' => 1,
            'timeout' => 15,
            ));
    }
}
