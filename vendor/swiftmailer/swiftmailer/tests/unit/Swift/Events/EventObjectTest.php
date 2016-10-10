<?php

class Swift_Events_EventObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testEventSourceCanBeReturnedViaGetter()
    {
        $source = new stdClass();
        $evt = $this->_createEvent($source);
        $ref = $evt->getSource();
        $this->assertEquals($source, $ref);
    }

    public function testEventDoesNotHaveCancelledBubbleWhenNew()
    {
        $source = new stdClass();
        $evt = $this->_createEvent($source);
        $this->assertFalse($evt->bubbleCancelled());
    }

    public function testBubbleCanBeCancelledInEvent()
    {
        $source = new stdClass();
        $evt = $this->_createEvent($source);
        $evt->cancelBubble();
        $this->assertTrue($evt->bubbleCancelled());
    }

    // -- Creation Methods

    private function _createEvent($source)
    {
        return new Swift_Events_EventObject($source);
    }
}
