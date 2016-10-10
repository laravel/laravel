<?php

class Swift_Transport_MailTransportTest extends \SwiftMailerTestCase
{
    public function testTransportInvokesMailOncePerMessage()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);

        $invoker->shouldReceive('mail')
                ->once();

        $transport->send($message);
    }

    public function testTransportUsesToFieldBodyInSending()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $to = $this->_createHeader();
        $headers = $this->_createHeaders(array(
            'To' => $to,
        ));
        $message = $this->_createMessage($headers);

        $to->shouldReceive('getFieldBody')
           ->zeroOrMoreTimes()
           ->andReturn('Foo <foo@bar>');
        $invoker->shouldReceive('mail')
                ->once()
                ->with('Foo <foo@bar>', \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function testTransportUsesSubjectFieldBodyInSending()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $subj = $this->_createHeader();
        $headers = $this->_createHeaders(array(
            'Subject' => $subj,
        ));
        $message = $this->_createMessage($headers);

        $subj->shouldReceive('getFieldBody')
             ->zeroOrMoreTimes()
             ->andReturn('Thing');
        $invoker->shouldReceive('mail')
                ->once()
                ->with(\Mockery::any(), 'Thing', \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function testTransportUsesBodyOfMessage()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);

        $message->shouldReceive('toString')
             ->zeroOrMoreTimes()
             ->andReturn(
                "To: Foo <foo@bar>\r\n".
                "\r\n".
                'This body'
             );
        $invoker->shouldReceive('mail')
                ->once()
                ->with(\Mockery::any(), \Mockery::any(), 'This body', \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function testTransportUsesHeadersFromMessage()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);

        $message->shouldReceive('toString')
             ->zeroOrMoreTimes()
             ->andReturn(
                "Subject: Stuff\r\n".
                "\r\n".
                'This body'
             );
        $invoker->shouldReceive('mail')
                ->once()
                ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), 'Subject: Stuff'.PHP_EOL, \Mockery::any());

        $transport->send($message);
    }

    public function testTransportReturnsCountOfAllRecipientsIfInvokerReturnsTrue()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);

        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array('foo@bar' => null, 'zip@button' => null));
        $message->shouldReceive('getCc')
                ->zeroOrMoreTimes()
                ->andReturn(array('test@test' => null));
        $invoker->shouldReceive('mail')
                ->once()
                ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any())
                ->andReturn(true);

        $this->assertEquals(3, $transport->send($message));
    }

    public function testTransportReturnsZeroIfInvokerReturnsFalse()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);

        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array('foo@bar' => null, 'zip@button' => null));
        $message->shouldReceive('getCc')
                ->zeroOrMoreTimes()
                ->andReturn(array('test@test' => null));
        $invoker->shouldReceive('mail')
                ->once()
                ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any())
                ->andReturn(false);

        $this->assertEquals(0, $transport->send($message));
    }

    public function testToHeaderIsRemovedFromHeaderSetDuringSending()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $to = $this->_createHeader();
        $headers = $this->_createHeaders(array(
            'To' => $to,
        ));
        $message = $this->_createMessage($headers);

        $headers->shouldReceive('remove')
                ->once()
                ->with('To');
        $headers->shouldReceive('remove')
                ->zeroOrMoreTimes();
        $invoker->shouldReceive('mail')
                ->once()
                ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function testSubjectHeaderIsRemovedFromHeaderSetDuringSending()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $subject = $this->_createHeader();
        $headers = $this->_createHeaders(array(
            'Subject' => $subject,
        ));
        $message = $this->_createMessage($headers);

        $headers->shouldReceive('remove')
                ->once()
                ->with('Subject');
        $headers->shouldReceive('remove')
                ->zeroOrMoreTimes();
        $invoker->shouldReceive('mail')
                ->once()
                ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function testToHeaderIsPutBackAfterSending()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $to = $this->_createHeader();
        $headers = $this->_createHeaders(array(
            'To' => $to,
        ));
        $message = $this->_createMessage($headers);

        $headers->shouldReceive('set')
                ->once()
                ->with($to);
        $headers->shouldReceive('set')
                ->zeroOrMoreTimes();
        $invoker->shouldReceive('mail')
                ->once()
                ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function testSubjectHeaderIsPutBackAfterSending()
    {
        $invoker = $this->_createInvoker();
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($invoker, $dispatcher);

        $subject = $this->_createHeader();
        $headers = $this->_createHeaders(array(
            'Subject' => $subject,
        ));
        $message = $this->_createMessage($headers);

        $headers->shouldReceive('set')
                ->once()
                ->with($subject);
        $headers->shouldReceive('set')
                ->zeroOrMoreTimes();
        $invoker->shouldReceive('mail')
                ->once()
                ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    // -- Creation Methods

    private function _createTransport($invoker, $dispatcher)
    {
        return new Swift_Transport_MailTransport($invoker, $dispatcher);
    }

    private function _createEventDispatcher()
    {
        return $this->getMockery('Swift_Events_EventDispatcher')->shouldIgnoreMissing();
    }

    private function _createInvoker()
    {
        return $this->getMockery('Swift_Transport_MailInvoker');
    }

    private function _createMessage($headers)
    {
        $message = $this->getMockery('Swift_Mime_Message')->shouldIgnoreMissing();
        $message->shouldReceive('getHeaders')
                ->zeroOrMoreTimes()
                ->andReturn($headers);

        return $message;
    }

    private function _createHeaders($headers = array())
    {
        $set = $this->getMockery('Swift_Mime_HeaderSet')->shouldIgnoreMissing();

        if (count($headers) > 0) {
            foreach ($headers as $name => $header) {
                $set->shouldReceive('get')
                    ->zeroOrMoreTimes()
                    ->with($name)
                    ->andReturn($header);
                $set->shouldReceive('has')
                    ->zeroOrMoreTimes()
                    ->with($name)
                    ->andReturn(true);
            }
        }

        $header = $this->_createHeader();
        $set->shouldReceive('get')
            ->zeroOrMoreTimes()
            ->andReturn($header);
        $set->shouldReceive('has')
            ->zeroOrMoreTimes()
            ->andReturn(true);

        return $set;
    }

    private function _createHeader()
    {
        return $this->getMockery('Swift_Mime_Header')->shouldIgnoreMissing();
    }
}
