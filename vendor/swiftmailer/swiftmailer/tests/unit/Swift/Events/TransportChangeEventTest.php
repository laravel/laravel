<?php

class Swift_Events_TransportChangeEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTransportReturnsTransport()
    {
        $transport = $this->_createTransport();
        $evt = $this->_createEvent($transport);
        $ref = $evt->getTransport();
        $this->assertEquals($transport, $ref);
    }

    public function testSourceIsTransport()
    {
        $transport = $this->_createTransport();
        $evt = $this->_createEvent($transport);
        $ref = $evt->getSource();
        $this->assertEquals($transport, $ref);
    }

    // -- Creation Methods

    private function _createEvent(Swift_Transport $source)
    {
        return new Swift_Events_TransportChangeEvent($source);
    }

    private function _createTransport()
    {
        return $this->getMock('Swift_Transport');
    }
}
