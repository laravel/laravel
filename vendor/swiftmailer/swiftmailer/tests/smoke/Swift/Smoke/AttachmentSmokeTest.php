<?php

/**
 * @group smoke
 */
class Swift_Smoke_AttachmentSmokeTest extends SwiftMailerSmokeTestCase
{
    public function setUp()
    {
        $this->_attFile = __DIR__.'/../../../_samples/files/textfile.zip';
    }

    public function testAttachmentSending()
    {
        $mailer = $this->_getMailer();
        $message = Swift_Message::newInstance()
            ->setSubject('[Swift Mailer] AttachmentSmokeTest')
            ->setFrom(array(SWIFT_SMOKE_EMAIL_ADDRESS => 'Swift Mailer'))
            ->setTo(SWIFT_SMOKE_EMAIL_ADDRESS)
            ->setBody('This message should contain an attached ZIP file (named "textfile.zip").'.PHP_EOL.
                'When unzipped, the archive should produce a text file which reads:'.PHP_EOL.
                '"This is part of a Swift Mailer v4 smoke test."'
                )
            ->attach(Swift_Attachment::fromPath($this->_attFile))
            ;
        $this->assertEquals(1, $mailer->send($message),
            '%s: The smoke test should send a single message'
            );
    }
}
