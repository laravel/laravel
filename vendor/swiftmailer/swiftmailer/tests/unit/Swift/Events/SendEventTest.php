<?php

class Swift_Events_SendEventTest extends \PHPUnit_Framework_TestCase
{
    public function testMessageCanBeFetchedViaGetter()
    {
        $message = $this->_createMessage();
        $transport = $this->_createTransport();

        $evt = $this->_createEvent($transport, $message);

        $ref = $evt->getMessage();
        $this->assertEquals($message, $ref,
            '%s: Message should be returned from getMessage()'
            );
    }

    public function testTransportCanBeFetchViaGetter()
    {
        $message = $this->_createMessage();
        $transport = $this->_createTransport();

        $evt = $this->_createEvent($transport, $message);

        $ref = $evt->getTransport();
        $this->assertEquals($transport, $ref,
            '%s: Transport should be returned from getTransport()'
            );
    }

    public function testTransportCanBeFetchViaGetSource()
    {
        $message = $this->_createMessage();
        $transport = $this->_createTransport();

        $evt = $this->_createEvent($transport, $message);

        $ref = $evt->getSource();
        $this->assertEquals($transport, $ref,
            '%s: Transport should be returned from getSource()'
            );
    }

    public function testResultCanBeSetAndGet()
    {
        $message = $this->_createMessage();
        $transport = $this->_createTransport();

        $evt = $this->_createEvent($transport, $message);

        $evt->setResult(
            Swift_Events_SendEvent::RESULT_SUCCESS | Swift_Events_SendEvent::RESULT_TENTATIVE
            );

        $this->assertTrue((bool) ($evt->getResult() & Swift_Events_SendEvent::RESULT_SUCCESS));
        $this->assertTrue((bool) ($evt->getResult() & Swift_Events_SendEvent::RESULT_TENTATIVE));
    }

    public function testFailedRecipientsCanBeSetAndGet()
    {
        $message = $this->_createMessage();
        $transport = $this->_createTransport();

        $evt = $this->_createEvent($transport, $message);

        $evt->setFailedRecipients(array('foo@bar', 'zip@button'));

        $this->assertEquals(array('foo@bar', 'zip@button'), $evt->getFailedRecipients(),
            '%s: FailedRecipients should be returned from getter'
            );
    }

    public function testFailedRecipientsGetsPickedUpCorrectly()
    {
        $message = $this->_createMessage();
        $transport = $this->_createTransport();

        $evt = $this->_createEvent($transport, $message);
        $this->assertEquals(array(), $evt->getFailedRecipients());
    }

    // -- Creation Methods

    private function _createEvent(Swift_Transport $source,
        Swift_Mime_Message $message)
    {
        return new Swift_Events_SendEvent($source, $message);
    }

    private function _createTransport()
    {
        return $this->getMock('Swift_Transport');
    }

    private function _createMessage()
    {
        return $this->getMock('Swift_Mime_Message');
    }
}
