<?php

/**
 * @group smoke
 */
class Swift_Smoke_BasicSmokeTest extends SwiftMailerSmokeTestCase
{
    public function testBasicSending()
    {
        $mailer = $this->_getMailer();
        $message = Swift_Message::newInstance()
            ->setSubject('[Swift Mailer] BasicSmokeTest')
            ->setFrom(array(SWIFT_SMOKE_EMAIL_ADDRESS => 'Swift Mailer'))
            ->setTo(SWIFT_SMOKE_EMAIL_ADDRESS)
            ->setBody('One, two, three, four, five...'.PHP_EOL.
                'six, seven, eight...'
                )
            ;
        $this->assertEquals(1, $mailer->send($message),
            '%s: The smoke test should send a single message'
            );
    }
}
