<?php

class Swift_Plugins_LoggerPluginTest extends \SwiftMailerTestCase
{
    public function testLoggerDelegatesAddingEntries()
    {
        $logger = $this->_createLogger();
        $logger->expects($this->once())
               ->method('add')
               ->with('foo');

        $plugin = $this->_createPlugin($logger);
        $plugin->add('foo');
    }

    public function testLoggerDelegatesDumpingEntries()
    {
        $logger = $this->_createLogger();
        $logger->expects($this->once())
               ->method('dump')
               ->will($this->returnValue('foobar'));

        $plugin = $this->_createPlugin($logger);
        $this->assertEquals('foobar', $plugin->dump());
    }

    public function testLoggerDelegatesClearingEntries()
    {
        $logger = $this->_createLogger();
        $logger->expects($this->once())
               ->method('clear');

        $plugin = $this->_createPlugin($logger);
        $plugin->clear();
    }

    public function testCommandIsSentToLogger()
    {
        $evt = $this->_createCommandEvent("foo\r\n");
        $logger = $this->_createLogger();
        $logger->expects($this->once())
               ->method('add')
               ->with($this->regExp('~foo\r\n~'));

        $plugin = $this->_createPlugin($logger);
        $plugin->commandSent($evt);
    }

    public function testResponseIsSentToLogger()
    {
        $evt = $this->_createResponseEvent("354 Go ahead\r\n");
        $logger = $this->_createLogger();
        $logger->expects($this->once())
               ->method('add')
               ->with($this->regExp('~354 Go ahead\r\n~'));

        $plugin = $this->_createPlugin($logger);
        $plugin->responseReceived($evt);
    }

    public function testTransportBeforeStartChangeIsSentToLogger()
    {
        $evt = $this->_createTransportChangeEvent();
        $logger = $this->_createLogger();
        $logger->expects($this->once())
               ->method('add')
               ->with($this->anything());

        $plugin = $this->_createPlugin($logger);
        $plugin->beforeTransportStarted($evt);
    }

    public function testTransportStartChangeIsSentToLogger()
    {
        $evt = $this->_createTransportChangeEvent();
        $logger = $this->_createLogger();
        $logger->expects($this->once())
               ->method('add')
               ->with($this->anything());

        $plugin = $this->_createPlugin($logger);
        $plugin->transportStarted($evt);
    }

    public function testTransportStopChangeIsSentToLogger()
    {
        $evt = $this->_createTransportChangeEvent();
        $logger = $this->_createLogger();
        $logger->expects($this->once())
               ->method('add')
               ->with($this->anything());

        $plugin = $this->_createPlugin($logger);
        $plugin->transportStopped($evt);
    }

    public function testTransportBeforeStopChangeIsSentToLogger()
    {
        $evt = $this->_createTransportChangeEvent();
        $logger = $this->_createLogger();
        $logger->expects($this->once())
               ->method('add')
               ->with($this->anything());

        $plugin = $this->_createPlugin($logger);
        $plugin->beforeTransportStopped($evt);
    }

    public function testExceptionsArePassedToDelegateAndLeftToBubbleUp()
    {
        $transport = $this->_createTransport();
        $evt = $this->_createTransportExceptionEvent();
        $logger = $this->_createLogger();
        $logger->expects($this->once())
               ->method('add')
               ->with($this->anything());

        $plugin = $this->_createPlugin($logger);
        try {
            $plugin->exceptionThrown($evt);
            $this->fail('Exception should bubble up.');
        } catch (Swift_TransportException $ex) {
        }
    }

    // -- Creation Methods

    private function _createLogger()
    {
        return $this->getMock('Swift_Plugins_Logger');
    }

    private function _createPlugin($logger)
    {
        return new Swift_Plugins_LoggerPlugin($logger);
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

    private function _createTransport()
    {
        return $this->getMock('Swift_Transport');
    }

    private function _createTransportChangeEvent()
    {
        $evt = $this->getMockBuilder('Swift_Events_TransportChangeEvent')
                    ->disableOriginalConstructor()
                    ->getMock();
        $evt->expects($this->any())
            ->method('getSource')
            ->will($this->returnValue($this->_createTransport()));

        return $evt;
    }

    public function _createTransportExceptionEvent()
    {
        $evt = $this->getMockBuilder('Swift_Events_TransportExceptionEvent')
                    ->disableOriginalConstructor()
                    ->getMock();
        $evt->expects($this->any())
            ->method('getException')
            ->will($this->returnValue(new Swift_TransportException('')));

        return $evt;
    }
}
