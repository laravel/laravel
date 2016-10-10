<?php

/**
 * @group smoke
 */
class Swift_Smoke_InternationalSmokeTest extends SwiftMailerSmokeTestCase
{
    public function setUp()
    {
        $this->_attFile = __DIR__.'/../../../_samples/files/textfile.zip';
    }

    public function testAttachmentSending()
    {
        $mailer = $this->_getMailer();
        $message = Swift_Message::newInstance()
            ->setCharset('utf-8')
            ->setSubject('[Swift Mailer] InternationalSmokeTest (διεθνής)')
            ->setFrom(array(SWIFT_SMOKE_EMAIL_ADDRESS => 'Χριστοφορου (Swift Mailer)'))
            ->setTo(SWIFT_SMOKE_EMAIL_ADDRESS)
            ->setBody('This message should contain an attached ZIP file (named "κείμενο, εδάφιο, θέμα.zip").'.PHP_EOL.
                'When unzipped, the archive should produce a text file which reads:'.PHP_EOL.
                '"This is part of a Swift Mailer v4 smoke test."'.PHP_EOL.
                PHP_EOL.
                'Following is some arbitrary Greek text:'.PHP_EOL.
                'Δεν βρέθηκαν λέξεις.'
                )
            ->attach(Swift_Attachment::fromPath($this->_attFile)
                ->setContentType('application/zip')
                ->setFilename('κείμενο, εδάφιο, θέμα.zip')
                )
            ;
        $this->assertEquals(1, $mailer->send($message),
            '%s: The smoke test should send a single message'
            );
    }
}
