<?php

use Mockery as m;

class Swift_Bug518Test extends \PHPUnit_Framework_TestCase
{
    public function testIfEmailChangesAfterQueued()
    {
        $failedRecipients = 'value';
        $message = new Swift_Message();
        $message->setTo('foo@bar.com');

        $that = $this;
        $messageValidation = function ($m) use ($that) {
            //the getTo should return the same value as we put in
            $that->assertEquals('foo@bar.com', key($m->getTo()), 'The message has changed after it was put to the memory queue');

            return true;
        };

        $transport = m::mock('Swift_Transport');
        $transport->shouldReceive('isStarted')->andReturn(true);
        $transport->shouldReceive('send')
            ->with(m::on($messageValidation), $failedRecipients)
            ->andReturn(1);

        $memorySpool = new Swift_MemorySpool();
        $memorySpool->queueMessage($message);

        /*
         * The message is queued in memory.
         * Lets change the message
         */
        $message->setTo('other@value.com');

        $memorySpool->flushQueue($transport, $failedRecipients);
    }
}
