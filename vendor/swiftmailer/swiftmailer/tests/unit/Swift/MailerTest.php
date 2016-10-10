<?php

class Swift_MailerTest extends \SwiftMailerTestCase
{
    public function testTransportIsStartedWhenSending()
    {
        $transport = $this->_createTransport();
        $message = $this->_createMessage();

        $started = false;
        $transport->shouldReceive('isStarted')
                  ->zeroOrMoreTimes()
                  ->andReturnUsing(function () use (&$started) {
                      return $started;
                  });
        $transport->shouldReceive('start')
                  ->once()
                  ->andReturnUsing(function () use (&$started) {
                      $started = true;

                      return;
                  });

        $mailer = $this->_createMailer($transport);
        $mailer->send($message);
    }

    public function testTransportIsOnlyStartedOnce()
    {
        $transport = $this->_createTransport();
        $message = $this->_createMessage();

        $started = false;
        $transport->shouldReceive('isStarted')
                  ->zeroOrMoreTimes()
                  ->andReturnUsing(function () use (&$started) {
                      return $started;
                  });
        $transport->shouldReceive('start')
                  ->once()
                  ->andReturnUsing(function () use (&$started) {
                      $started = true;

                      return;
                  });

        $mailer = $this->_createMailer($transport);
        for ($i = 0; $i < 10; ++$i) {
            $mailer->send($message);
        }
    }

    public function testMessageIsPassedToTransport()
    {
        $transport = $this->_createTransport();
        $message = $this->_createMessage();
        $transport->shouldReceive('send')
                  ->once()
                  ->with($message, \Mockery::any());

        $mailer = $this->_createMailer($transport);
        $mailer->send($message);
    }

    public function testSendReturnsCountFromTransport()
    {
        $transport = $this->_createTransport();
        $message = $this->_createMessage();
        $transport->shouldReceive('send')
                  ->once()
                  ->with($message, \Mockery::any())
                  ->andReturn(57);

        $mailer = $this->_createMailer($transport);
        $this->assertEquals(57, $mailer->send($message));
    }

    public function testFailedRecipientReferenceIsPassedToTransport()
    {
        $failures = array();

        $transport = $this->_createTransport();
        $message = $this->_createMessage();
        $transport->shouldReceive('send')
                  ->once()
                  ->with($message, $failures)
                  ->andReturn(57);

        $mailer = $this->_createMailer($transport);
        $mailer->send($message, $failures);
    }

    public function testSendRecordsRfcComplianceExceptionAsEntireSendFailure()
    {
        $failures = array();

        $rfcException = new Swift_RfcComplianceException('test');
        $transport = $this->_createTransport();
        $message = $this->_createMessage();
        $message->shouldReceive('getTo')
                  ->once()
                  ->andReturn(array('foo&invalid' => 'Foo', 'bar@valid.tld' => 'Bar'));
        $transport->shouldReceive('send')
                  ->once()
                  ->with($message, $failures)
                  ->andThrow($rfcException);

        $mailer = $this->_createMailer($transport);
        $this->assertEquals(0, $mailer->send($message, $failures), '%s: Should return 0');
        $this->assertEquals(array('foo&invalid', 'bar@valid.tld'), $failures, '%s: Failures should contain all addresses since the entire message failed to compile');
    }

    public function testRegisterPluginDelegatesToTransport()
    {
        $plugin = $this->_createPlugin();
        $transport = $this->_createTransport();
        $mailer = $this->_createMailer($transport);

        $transport->shouldReceive('registerPlugin')
                  ->once()
                  ->with($plugin);

        $mailer->registerPlugin($plugin);
    }

    // -- Creation methods

    private function _createPlugin()
    {
        return $this->getMockery('Swift_Events_EventListener')->shouldIgnoreMissing();
    }

    private function _createTransport()
    {
        return $this->getMockery('Swift_Transport')->shouldIgnoreMissing();
    }

    private function _createMessage()
    {
        return $this->getMockery('Swift_Mime_Message')->shouldIgnoreMissing();
    }

    private function _createIterator()
    {
        return $this->getMockery('Swift_Mailer_RecipientIterator')->shouldIgnoreMissing();
    }

    private function _createMailer(Swift_Transport $transport)
    {
        return new Swift_Mailer($transport);
    }
}
