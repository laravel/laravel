<?php

class Swift_Events_SimpleEventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    private $_dispatcher;

    public function setUp()
    {
        $this->_dispatcher = new Swift_Events_SimpleEventDispatcher();
    }

    public function testSendEventCanBeCreated()
    {
        $transport = $this->getMock('Swift_Transport');
        $message = $this->getMock('Swift_Mime_Message');
        $evt = $this->_dispatcher->createSendEvent($transport, $message);
        $this->assertInstanceof('Swift_Events_SendEvent', $evt);
        $this->assertSame($message, $evt->getMessage());
        $this->assertSame($transport, $evt->getTransport());
    }

    public function testCommandEventCanBeCreated()
    {
        $buf = $this->getMock('Swift_Transport');
        $evt = $this->_dispatcher->createCommandEvent($buf, "FOO\r\n", array(250));
        $this->assertInstanceof('Swift_Events_CommandEvent', $evt);
        $this->assertSame($buf, $evt->getSource());
        $this->assertEquals("FOO\r\n", $evt->getCommand());
        $this->assertEquals(array(250), $evt->getSuccessCodes());
    }

    public function testResponseEventCanBeCreated()
    {
        $buf = $this->getMock('Swift_Transport');
        $evt = $this->_dispatcher->createResponseEvent($buf, "250 Ok\r\n", true);
        $this->assertInstanceof('Swift_Events_ResponseEvent', $evt);
        $this->assertSame($buf, $evt->getSource());
        $this->assertEquals("250 Ok\r\n", $evt->getResponse());
        $this->assertTrue($evt->isValid());
    }

    public function testTransportChangeEventCanBeCreated()
    {
        $transport = $this->getMock('Swift_Transport');
        $evt = $this->_dispatcher->createTransportChangeEvent($transport);
        $this->assertInstanceof('Swift_Events_TransportChangeEvent', $evt);
        $this->assertSame($transport, $evt->getSource());
    }

    public function testTransportExceptionEventCanBeCreated()
    {
        $transport = $this->getMock('Swift_Transport');
        $ex = new Swift_TransportException('');
        $evt = $this->_dispatcher->createTransportExceptionEvent($transport, $ex);
        $this->assertInstanceof('Swift_Events_TransportExceptionEvent', $evt);
        $this->assertSame($transport, $evt->getSource());
        $this->assertSame($ex, $evt->getException());
    }

    public function testListenersAreNotifiedOfDispatchedEvent()
    {
        $transport = $this->getMock('Swift_Transport');

        $evt = $this->_dispatcher->createTransportChangeEvent($transport);

        $listenerA = $this->getMock('Swift_Events_TransportChangeListener');
        $listenerB = $this->getMock('Swift_Events_TransportChangeListener');

        $this->_dispatcher->bindEventListener($listenerA);
        $this->_dispatcher->bindEventListener($listenerB);

        $listenerA->expects($this->once())
                  ->method('transportStarted')
                  ->with($evt);
        $listenerB->expects($this->once())
                  ->method('transportStarted')
                  ->with($evt);

        $this->_dispatcher->dispatchEvent($evt, 'transportStarted');
    }

    public function testListenersAreOnlyCalledIfImplementingCorrectInterface()
    {
        $transport = $this->getMock('Swift_Transport');
        $message = $this->getMock('Swift_Mime_Message');

        $evt = $this->_dispatcher->createSendEvent($transport, $message);

        $targetListener = $this->getMock('Swift_Events_SendListener');
        $otherListener = $this->getMock('Swift_Events_TransportChangeListener');

        $this->_dispatcher->bindEventListener($targetListener);
        $this->_dispatcher->bindEventListener($otherListener);

        $targetListener->expects($this->once())
                       ->method('sendPerformed')
                       ->with($evt);
        $otherListener->expects($this->never())
                    ->method('sendPerformed');

        $this->_dispatcher->dispatchEvent($evt, 'sendPerformed');
    }

    public function testListenersCanCancelBubblingOfEvent()
    {
        $transport = $this->getMock('Swift_Transport');
        $message = $this->getMock('Swift_Mime_Message');

        $evt = $this->_dispatcher->createSendEvent($transport, $message);

        $listenerA = $this->getMock('Swift_Events_SendListener');
        $listenerB = $this->getMock('Swift_Events_SendListener');

        $this->_dispatcher->bindEventListener($listenerA);
        $this->_dispatcher->bindEventListener($listenerB);

        $listenerA->expects($this->once())
                  ->method('sendPerformed')
                  ->with($evt)
                  ->will($this->returnCallback(function ($object) {
                      $object->cancelBubble(true);
                  }));
        $listenerB->expects($this->never())
                  ->method('sendPerformed');

        $this->_dispatcher->dispatchEvent($evt, 'sendPerformed');

        $this->assertTrue($evt->bubbleCancelled());
    }

    private function _createDispatcher(array $map)
    {
        return new Swift_Events_SimpleEventDispatcher($map);
    }
}
