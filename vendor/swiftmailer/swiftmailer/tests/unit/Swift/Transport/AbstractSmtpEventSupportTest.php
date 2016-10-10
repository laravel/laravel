<?php

require_once __DIR__.'/AbstractSmtpTest.php';

abstract class Swift_Transport_AbstractSmtpEventSupportTest extends Swift_Transport_AbstractSmtpTest
{
    public function testRegisterPluginLoadsPluginInEventDispatcher()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $listener = $this->getMockery('Swift_Events_EventListener');
        $smtp = $this->_getTransport($buf, $dispatcher);
        $dispatcher->shouldReceive('bindEventListener')
                   ->once()
                   ->with($listener);

        $smtp->registerPlugin($listener);
    }

    public function testSendingDispatchesBeforeSendEvent()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $message = $this->_createMessage();
        $smtp = $this->_getTransport($buf, $dispatcher);
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();

        $message->shouldReceive('getFrom')
                ->zeroOrMoreTimes()
                ->andReturn(array('chris@swiftmailer.org' => null));
        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array('mark@swiftmailer.org' => 'Mark'));
        $dispatcher->shouldReceive('createSendEvent')
                   ->once()
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->once()
                   ->with($evt, 'beforeSendPerformed');
        $dispatcher->shouldReceive('dispatchEvent')
                   ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->zeroOrMoreTimes()
            ->andReturn(false);

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(1, $smtp->send($message));
    }

    public function testSendingDispatchesSendEvent()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $message = $this->_createMessage();
        $smtp = $this->_getTransport($buf, $dispatcher);
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();

        $message->shouldReceive('getFrom')
                ->zeroOrMoreTimes()
                ->andReturn(array('chris@swiftmailer.org' => null));
        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array('mark@swiftmailer.org' => 'Mark'));
        $dispatcher->shouldReceive('createSendEvent')
                   ->once()
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->once()
                   ->with($evt, 'sendPerformed');
        $dispatcher->shouldReceive('dispatchEvent')
                   ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->zeroOrMoreTimes()
            ->andReturn(false);

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(1, $smtp->send($message));
    }

    public function testSendEventCapturesFailures()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();
        $smtp = $this->_getTransport($buf, $dispatcher);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->zeroOrMoreTimes()
                ->andReturn(array('chris@swiftmailer.org' => null));
        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array('mark@swiftmailer.org' => 'Mark'));
        $buf->shouldReceive('write')
            ->once()
            ->with("MAIL FROM:<chris@swiftmailer.org>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<mark@swiftmailer.org>\r\n")
            ->andReturn(2);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(2)
            ->andReturn("500 Not now\r\n");
        $dispatcher->shouldReceive('createSendEvent')
                   ->zeroOrMoreTimes()
                   ->with($smtp, \Mockery::any())
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->once()
                   ->with($evt, 'sendPerformed');
        $dispatcher->shouldReceive('dispatchEvent')
                   ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->zeroOrMoreTimes()
            ->andReturn(false);
        $evt->shouldReceive('setFailedRecipients')
            ->once()
            ->with(array('mark@swiftmailer.org'));

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(0, $smtp->send($message));
    }

    public function testSendEventHasResultFailedIfAllFailures()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();
        $smtp = $this->_getTransport($buf, $dispatcher);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->zeroOrMoreTimes()
                ->andReturn(array('chris@swiftmailer.org' => null));
        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array('mark@swiftmailer.org' => 'Mark'));
        $buf->shouldReceive('write')
            ->once()
            ->with("MAIL FROM:<chris@swiftmailer.org>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<mark@swiftmailer.org>\r\n")
            ->andReturn(2);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(2)
            ->andReturn("500 Not now\r\n");
        $dispatcher->shouldReceive('createSendEvent')
                   ->zeroOrMoreTimes()
                   ->with($smtp, \Mockery::any())
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->once()
                   ->with($evt, 'sendPerformed');
        $dispatcher->shouldReceive('dispatchEvent')
                   ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->zeroOrMoreTimes()
            ->andReturn(false);
        $evt->shouldReceive('setResult')
            ->once()
            ->with(Swift_Events_SendEvent::RESULT_FAILED);

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(0, $smtp->send($message));
    }

    public function testSendEventHasResultTentativeIfSomeFailures()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();
        $smtp = $this->_getTransport($buf, $dispatcher);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->zeroOrMoreTimes()
                ->andReturn(array('chris@swiftmailer.org' => null));
        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array(
                    'mark@swiftmailer.org' => 'Mark',
                    'chris@site.tld' => 'Chris',
                ));
        $buf->shouldReceive('write')
            ->once()
            ->with("MAIL FROM:<chris@swiftmailer.org>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<mark@swiftmailer.org>\r\n")
            ->andReturn(2);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(2)
            ->andReturn("500 Not now\r\n");
        $dispatcher->shouldReceive('createSendEvent')
                   ->zeroOrMoreTimes()
                   ->with($smtp, \Mockery::any())
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->once()
                   ->with($evt, 'sendPerformed');
        $dispatcher->shouldReceive('dispatchEvent')
                   ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->zeroOrMoreTimes()
            ->andReturn(false);
        $evt->shouldReceive('setResult')
            ->once()
            ->with(Swift_Events_SendEvent::RESULT_TENTATIVE);

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(1, $smtp->send($message));
    }

    public function testSendEventHasResultSuccessIfNoFailures()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();
        $smtp = $this->_getTransport($buf, $dispatcher);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->zeroOrMoreTimes()
                ->andReturn(array('chris@swiftmailer.org' => null));
        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array(
                    'mark@swiftmailer.org' => 'Mark',
                    'chris@site.tld' => 'Chris',
                ));
        $dispatcher->shouldReceive('createSendEvent')
                   ->zeroOrMoreTimes()
                   ->with($smtp, \Mockery::any())
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->once()
                   ->with($evt, 'sendPerformed');
        $dispatcher->shouldReceive('dispatchEvent')
                   ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->zeroOrMoreTimes()
            ->andReturn(false);
        $evt->shouldReceive('setResult')
            ->once()
            ->with(Swift_Events_SendEvent::RESULT_SUCCESS);

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(2, $smtp->send($message));
    }

    public function testCancellingEventBubbleBeforeSendStopsEvent()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();
        $smtp = $this->_getTransport($buf, $dispatcher);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->zeroOrMoreTimes()
                ->andReturn(array('chris@swiftmailer.org' => null));
        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array('mark@swiftmailer.org' => 'Mark'));
        $dispatcher->shouldReceive('createSendEvent')
                   ->zeroOrMoreTimes()
                   ->with($smtp, \Mockery::any())
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->once()
                   ->with($evt, 'beforeSendPerformed');
        $dispatcher->shouldReceive('dispatchEvent')
                   ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->atLeast()->once()
            ->andReturn(true);

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(0, $smtp->send($message));
    }

    public function testStartingTransportDispatchesTransportChangeEvent()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportChangeEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $dispatcher->shouldReceive('createTransportChangeEvent')
                   ->atLeast()->once()
                   ->with($smtp)
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->once()
                   ->with($evt, 'transportStarted');
        $dispatcher->shouldReceive('dispatchEvent')
                   ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->atLeast()->once()
            ->andReturn(false);

        $this->_finishBuffer($buf);
        $smtp->start();
    }

    public function testStartingTransportDispatchesBeforeTransportChangeEvent()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportChangeEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $dispatcher->shouldReceive('createTransportChangeEvent')
                   ->atLeast()->once()
                   ->with($smtp)
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->once()
                   ->with($evt, 'beforeTransportStarted');
        $dispatcher->shouldReceive('dispatchEvent')
                   ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->atLeast()->once()
            ->andReturn(false);

        $this->_finishBuffer($buf);
        $smtp->start();
    }

    public function testCancellingBubbleBeforeTransportStartStopsEvent()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportChangeEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $dispatcher->shouldReceive('createTransportChangeEvent')
                   ->atLeast()->once()
                   ->with($smtp)
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->once()
                   ->with($evt, 'beforeTransportStarted');
        $dispatcher->shouldReceive('dispatchEvent')
                   ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->atLeast()->once()
            ->andReturn(true);

        $this->_finishBuffer($buf);
        $smtp->start();

        $this->assertFalse($smtp->isStarted(),
            '%s: Transport should not be started since event bubble was cancelled'
        );
    }

    public function testStoppingTransportDispatchesTransportChangeEvent()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportChangeEvent')->shouldIgnoreMissing();
        $smtp = $this->_getTransport($buf, $dispatcher);

        $dispatcher->shouldReceive('createTransportChangeEvent')
                   ->atLeast()->once()
                   ->with($smtp)
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->once()
                   ->with($evt, 'transportStopped');
        $dispatcher->shouldReceive('dispatchEvent')
                   ->zeroOrMoreTimes();

        $this->_finishBuffer($buf);
        $smtp->start();
        $smtp->stop();
    }

    public function testStoppingTransportDispatchesBeforeTransportChangeEvent()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportChangeEvent')->shouldIgnoreMissing();
        $smtp = $this->_getTransport($buf, $dispatcher);

        $dispatcher->shouldReceive('createTransportChangeEvent')
                   ->atLeast()->once()
                   ->with($smtp)
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->once()
                   ->with($evt, 'beforeTransportStopped');
        $dispatcher->shouldReceive('dispatchEvent')
                   ->zeroOrMoreTimes();

        $this->_finishBuffer($buf);
        $smtp->start();
        $smtp->stop();
    }

    public function testCancellingBubbleBeforeTransportStoppedStopsEvent()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportChangeEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $hasRun = false;
        $dispatcher->shouldReceive('createTransportChangeEvent')
                   ->atLeast()->once()
                   ->with($smtp)
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->once()
                   ->with($evt, 'beforeTransportStopped')
                   ->andReturnUsing(function () use (&$hasRun) {
                       $hasRun = true;
                   });
        $dispatcher->shouldReceive('dispatchEvent')
                   ->zeroOrMoreTimes();
        $evt->shouldReceive('bubbleCancelled')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function () use (&$hasRun) {
                return $hasRun;
            });

        $this->_finishBuffer($buf);
        $smtp->start();
        $smtp->stop();

        $this->assertTrue($smtp->isStarted(),
            '%s: Transport should not be stopped since event bubble was cancelled'
        );
    }

    public function testResponseEventsAreGenerated()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_ResponseEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $dispatcher->shouldReceive('createResponseEvent')
                   ->atLeast()->once()
                   ->with($smtp, \Mockery::any(), \Mockery::any())
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->atLeast()->once()
                   ->with($evt, 'responseReceived');

        $this->_finishBuffer($buf);
        $smtp->start();
    }

    public function testCommandEventsAreGenerated()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_CommandEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $dispatcher->shouldReceive('createCommandEvent')
                   ->once()
                   ->with($smtp, \Mockery::any(), \Mockery::any())
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->once()
                   ->with($evt, 'commandSent');

        $this->_finishBuffer($buf);
        $smtp->start();
    }

    public function testExceptionsCauseExceptionEvents()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportExceptionEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $buf->shouldReceive('readLine')
            ->atLeast()->once()
            ->andReturn("503 I'm sleepy, go away!\r\n");
        $dispatcher->shouldReceive('createTransportExceptionEvent')
                   ->zeroOrMoreTimes()
                   ->with($smtp, \Mockery::any())
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->once()
                   ->with($evt, 'exceptionThrown');
        $evt->shouldReceive('bubbleCancelled')
            ->atLeast()->once()
            ->andReturn(false);

        try {
            $smtp->start();
            $this->fail('TransportException should be thrown on invalid response');
        } catch (Swift_TransportException $e) {
        }
    }

    public function testExceptionBubblesCanBeCancelled()
    {
        $buf = $this->_getBuffer();
        $dispatcher = $this->_createEventDispatcher(false);
        $evt = $this->getMockery('Swift_Events_TransportExceptionEvent');
        $smtp = $this->_getTransport($buf, $dispatcher);

        $buf->shouldReceive('readLine')
            ->atLeast()->once()
            ->andReturn("503 I'm sleepy, go away!\r\n");
        $dispatcher->shouldReceive('createTransportExceptionEvent')
                   ->twice()
                   ->with($smtp, \Mockery::any())
                   ->andReturn($evt);
        $dispatcher->shouldReceive('dispatchEvent')
                   ->twice()
                   ->with($evt, 'exceptionThrown');
        $evt->shouldReceive('bubbleCancelled')
            ->atLeast()->once()
            ->andReturn(true);

        $this->_finishBuffer($buf);
        $smtp->start();
    }

    // -- Creation Methods

    protected function _createEventDispatcher($stub = true)
    {
        return $this->getMockery('Swift_Events_EventDispatcher')->shouldIgnoreMissing();
    }
}
