<?php

require_once __DIR__.'/AbstractStreamBufferAcceptanceTest.php';

class Swift_Transport_StreamBuffer_SslSocketAcceptanceTest
    extends Swift_Transport_StreamBuffer_AbstractStreamBufferAcceptanceTest
{
    public function setUp()
    {
        $streams = stream_get_transports();
        if (!in_array('ssl', $streams)) {
            $this->markTestSkipped(
                'SSL is not configured for your system.  It is not possible to run this test'
             );
        }
        if (!defined('SWIFT_SSL_HOST')) {
            $this->markTestSkipped(
                'Cannot run test without an SSL enabled SMTP host to connect to (define '.
                'SWIFT_SSL_HOST in tests/acceptance.conf.php if you wish to run this test)'
             );
        }

        parent::setUp();
    }

    protected function _initializeBuffer()
    {
        $parts = explode(':', SWIFT_SSL_HOST);
        $host = $parts[0];
        $port = isset($parts[1]) ? $parts[1] : 25;

        $this->_buffer->initialize(array(
            'type' => Swift_Transport_IoBuffer::TYPE_SOCKET,
            'host' => $host,
            'port' => $port,
            'protocol' => 'ssl',
            'blocking' => 1,
            'timeout' => 15,
            ));
    }
}
