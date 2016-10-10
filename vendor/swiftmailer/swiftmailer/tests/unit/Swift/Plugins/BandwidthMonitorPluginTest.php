<?php

class Swift_Plugins_BandwidthMonitorPluginTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_monitor = new Swift_Plugins_BandwidthMonitorPlugin();
    }

    public function testBytesOutIncreasesWhenCommandsSent()
    {
        $evt = $this->_createCommandEvent("RCPT TO:<foo@bar.com>\r\n");

        $this->assertEquals(0, $this->_monitor->getBytesOut());
        $this->_monitor->commandSent($evt);
        $this->assertEquals(23, $this->_monitor->getBytesOut());
        $this->_monitor->commandSent($evt);
        $this->assertEquals(46, $this->_monitor->getBytesOut());
    }

    public function testBytesInIncreasesWhenResponsesReceived()
    {
        $evt = $this->_createResponseEvent("250 Ok\r\n");

        $this->assertEquals(0, $this->_monitor->getBytesIn());
        $this->_monitor->responseReceived($evt);
        $this->assertEquals(8, $this->_monitor->getBytesIn());
        $this->_monitor->responseReceived($evt);
        $this->assertEquals(16, $this->_monitor->getBytesIn());
    }

    public function testCountersCanBeReset()
    {
        $evt = $this->_createResponseEvent("250 Ok\r\n");

        $this->assertEquals(0, $this->_monitor->getBytesIn());
        $this->_monitor->responseReceived($evt);
        $this->assertEquals(8, $this->_monitor->getBytesIn());
        $this->_monitor->responseReceived($evt);
        $this->assertEquals(16, $this->_monitor->getBytesIn());

        $evt = $this->_createCommandEvent("RCPT TO:<foo@bar.com>\r\n");

        $this->assertEquals(0, $this->_monitor->getBytesOut());
        $this->_monitor->commandSent($evt);
        $this->assertEquals(23, $this->_monitor->getBytesOut());
        $this->_monitor->commandSent($evt);
        $this->assertEquals(46, $this->_monitor->getBytesOut());

        $this->_monitor->reset();

        $this->assertEquals(0, $this->_monitor->getBytesOut());
        $this->assertEquals(0, $this->_monitor->getBytesIn());
    }

    public function testBytesOutIncreasesAccordingToMessageLength()
    {
        $message = $this->_createMessageWithByteCount(6);
        $evt = $this->_createSendEvent($message);

        $this->assertEquals(0, $this->_monitor->getBytesOut());
        $this->_monitor->sendPerformed($evt);
        $this->assertEquals(6, $this->_monitor->getBytesOut());
        $this->_monitor->sendPerformed($evt);
        $this->assertEquals(12, $this->_monitor->getBytesOut());
    }

    // -- Creation Methods

    private function _createSendEvent($message)
    {
        $evt = $this->getMockBuilder('Swift_Events_SendEvent')
                    ->disableOriginalConstructor()
                    ->getMock();
        $evt->expects($this->any())
            ->method('getMessage')
            ->will($this->returnValue($message));

        return $evt;
    }

    private function _createCommandEvent($command)
    {
        $evt = $this->getMockBuilder('Swift_Events_CommandEvent')
                    ->disableOriginalConstructor()
                    ->getMock();
        $evt->expects($this->any())
            ->method('getCommand')
            ->will($this->returnValue($command));

        return $evt;
    }

    private function _createResponseEvent($response)
    {
        $evt = $this->getMockBuilder('Swift_Events_ResponseEvent')
                    ->disableOriginalConstructor()
                    ->getMock();
        $evt->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($response));

        return $evt;
    }

    private function _createMessageWithByteCount($bytes)
    {
        $this->_bytes = $bytes;
        $msg = $this->getMock('Swift_Mime_Message');
        $msg->expects($this->any())
            ->method('toByteStream')
            ->will($this->returnCallback(array($this, '_write')));
      /*  $this->_checking(Expectations::create()
            -> ignoring($msg)->toByteStream(any()) -> calls(array($this, '_write'))
        ); */

        return $msg;
    }

    private $_bytes = 0;
    public function _write($is)
    {
        for ($i = 0; $i < $this->_bytes; ++$i) {
            $is->write('x');
        }
    }
}
