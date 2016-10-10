<?php

use Mockery as m;

class Swift_Bug534Test extends \PHPUnit_Framework_TestCase
{
    public function testEmbeddedImagesAreEmbedded()
    {
        $message = Swift_Message::newInstance()
            ->setFrom('from@example.com')
            ->setTo('to@example.com')
            ->setSubject('test')
        ;
        $cid = $message->embed(Swift_Image::fromPath(__DIR__.'/../../_samples/files/swiftmailer.png'));
        $message->setBody('<img src="'.$cid.'" />', 'text/html');

        $that = $this;
        $messageValidation = function (Swift_Mime_Message $message) use ($that) {
            preg_match('/cid:(.*)"/', $message->toString(), $matches);
            $cid = $matches[1];
            preg_match('/Content-ID: <(.*)>/', $message->toString(), $matches);
            $contentId = $matches[1];
            $that->assertEquals($cid, $contentId, 'cid in body and mime part Content-ID differ');

            return true;
        };

        $failedRecipients = array();

        $transport = m::mock('Swift_Transport');
        $transport->shouldReceive('isStarted')->andReturn(true);
        $transport->shouldReceive('send')->with(m::on($messageValidation), $failedRecipients)->andReturn(1);

        $memorySpool = new Swift_MemorySpool();
        $memorySpool->queueMessage($message);
        $memorySpool->flushQueue($transport, $failedRecipients);
    }
}
