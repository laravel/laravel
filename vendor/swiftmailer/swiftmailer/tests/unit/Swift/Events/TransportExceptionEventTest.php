<?php

class Swift_Events_TransportExceptionEventTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionCanBeFetchViaGetter()
    {
        $ex = $this->_createException();
        $transport = $this->_createTransport();
        $evt = $this->_createEvent($transport, $ex);
        $ref = $evt->getException();
        $this->assertEquals($ex, $ref,
            '%s: Exception should be available via getException()'
            );
    }

    public function testSourceIsTransport()
    {
        $ex = $this->_createException();
        $transport = $this->_createTransport();
        $evt = $this->_createEvent($transport, $ex);
        $ref = $evt->getSource();
        $this->assertEquals($transport, $ref,
            '%s: Transport should be available via getSource()'
            );
    }

    // -- Creation Methods

    private function _createEvent(Swift_Transport $transport, Swift_TransportException $ex)
    {
        return new Swift_Events_TransportExceptionEvent($transport, $ex);
    }

    private function _createTransport()
    {
        return $this->getMock('Swift_Transport');
    }

    private function _createException()
    {
        return new Swift_TransportException('');
    }
}
