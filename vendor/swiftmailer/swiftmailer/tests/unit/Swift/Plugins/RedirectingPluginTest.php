<?php

class Swift_Plugins_RedirectingPluginTest extends \PHPUnit_Framework_TestCase
{
    public function testRecipientCanBeSetAndFetched()
    {
        $plugin = new Swift_Plugins_RedirectingPlugin('fabien@example.com');
        $this->assertEquals('fabien@example.com', $plugin->getRecipient());
        $plugin->setRecipient('chris@example.com');
        $this->assertEquals('chris@example.com', $plugin->getRecipient());
    }

    public function testPluginChangesRecipients()
    {
        $message = Swift_Message::newInstance()
            ->setSubject('...')
            ->setFrom(array('john@example.com' => 'John Doe'))
            ->setTo($to = array(
                'fabien-to@example.com' => 'Fabien (To)',
                'chris-to@example.com' => 'Chris (To)',
            ))
            ->setCc($cc = array(
                'fabien-cc@example.com' => 'Fabien (Cc)',
                'chris-cc@example.com' => 'Chris (Cc)',
            ))
            ->setBcc($bcc = array(
                'fabien-bcc@example.com' => 'Fabien (Bcc)',
                'chris-bcc@example.com' => 'Chris (Bcc)',
            ))
            ->setBody('...')
        ;

        $plugin = new Swift_Plugins_RedirectingPlugin('god@example.com');

        $evt = $this->_createSendEvent($message);

        $plugin->beforeSendPerformed($evt);

        $this->assertEquals($message->getTo(), array('god@example.com' => ''));
        $this->assertEquals($message->getCc(), array());
        $this->assertEquals($message->getBcc(), array());

        $plugin->sendPerformed($evt);

        $this->assertEquals($message->getTo(), $to);
        $this->assertEquals($message->getCc(), $cc);
        $this->assertEquals($message->getBcc(), $bcc);
    }

    public function testPluginRespectsUnsetToList()
    {
        $message = Swift_Message::newInstance()
            ->setSubject('...')
            ->setFrom(array('john@example.com' => 'John Doe'))
            ->setCc($cc = array(
                'fabien-cc@example.com' => 'Fabien (Cc)',
                'chris-cc@example.com' => 'Chris (Cc)',
            ))
            ->setBcc($bcc = array(
                'fabien-bcc@example.com' => 'Fabien (Bcc)',
                'chris-bcc@example.com' => 'Chris (Bcc)',
            ))
            ->setBody('...')
        ;

        $plugin = new Swift_Plugins_RedirectingPlugin('god@example.com');

        $evt = $this->_createSendEvent($message);

        $plugin->beforeSendPerformed($evt);

        $this->assertEquals($message->getTo(), array('god@example.com' => ''));
        $this->assertEquals($message->getCc(), array());
        $this->assertEquals($message->getBcc(), array());

        $plugin->sendPerformed($evt);

        $this->assertEquals($message->getTo(), array());
        $this->assertEquals($message->getCc(), $cc);
        $this->assertEquals($message->getBcc(), $bcc);
    }

    public function testPluginRespectsAWhitelistOfPatterns()
    {
        $message = Swift_Message::newInstance()
            ->setSubject('...')
            ->setFrom(array('john@example.com' => 'John Doe'))
            ->setTo($to = array(
                'fabien-to@example.com' => 'Fabien (To)',
                'chris-to@example.com' => 'Chris (To)',
                'lars-to@internal.com' => 'Lars (To)',
            ))
            ->setCc($cc = array(
                'fabien-cc@example.com' => 'Fabien (Cc)',
                'chris-cc@example.com' => 'Chris (Cc)',
                'lars-cc@internal.org' => 'Lars (Cc)',
            ))
            ->setBcc($bcc = array(
                'fabien-bcc@example.com' => 'Fabien (Bcc)',
                'chris-bcc@example.com' => 'Chris (Bcc)',
                'john-bcc@example.org' => 'John (Bcc)',
            ))
            ->setBody('...')
        ;

        $recipient = 'god@example.com';
        $patterns = array('/^.*@internal.[a-z]+$/', '/^john-.*$/');

        $plugin = new Swift_Plugins_RedirectingPlugin($recipient, $patterns);

        $this->assertEquals($recipient, $plugin->getRecipient());
        $this->assertEquals($plugin->getWhitelist(), $patterns);

        $evt = $this->_createSendEvent($message);

        $plugin->beforeSendPerformed($evt);

        $this->assertEquals($message->getTo(), array('lars-to@internal.com' => 'Lars (To)', 'god@example.com' => null));
        $this->assertEquals($message->getCc(), array('lars-cc@internal.org' => 'Lars (Cc)'));
        $this->assertEquals($message->getBcc(), array('john-bcc@example.org' => 'John (Bcc)'));

        $plugin->sendPerformed($evt);

        $this->assertEquals($message->getTo(), $to);
        $this->assertEquals($message->getCc(), $cc);
        $this->assertEquals($message->getBcc(), $bcc);
    }

    public function testArrayOfRecipientsCanBeExplicitlyDefined()
    {
        $message = Swift_Message::newInstance()
            ->setSubject('...')
            ->setFrom(array('john@example.com' => 'John Doe'))
            ->setTo(array(
            'fabien@example.com' => 'Fabien',
            'chris@example.com' => 'Chris (To)',
            'lars-to@internal.com' => 'Lars (To)',
        ))
            ->setCc(array(
            'fabien@example.com' => 'Fabien',
            'chris-cc@example.com' => 'Chris (Cc)',
            'lars-cc@internal.org' => 'Lars (Cc)',
        ))
            ->setBcc(array(
            'fabien@example.com' => 'Fabien',
            'chris-bcc@example.com' => 'Chris (Bcc)',
            'john-bcc@example.org' => 'John (Bcc)',
        ))
            ->setBody('...')
        ;

        $recipients = array('god@example.com', 'fabien@example.com');
        $patterns = array('/^.*@internal.[a-z]+$/');

        $plugin = new Swift_Plugins_RedirectingPlugin($recipients, $patterns);

        $evt = $this->_createSendEvent($message);

        $plugin->beforeSendPerformed($evt);

        $this->assertEquals(
            $message->getTo(),
            array('fabien@example.com' => 'Fabien', 'lars-to@internal.com' => 'Lars (To)', 'god@example.com' => null)
        );
        $this->assertEquals(
            $message->getCc(),
            array('fabien@example.com' => 'Fabien', 'lars-cc@internal.org' => 'Lars (Cc)')
        );
        $this->assertEquals($message->getBcc(), array('fabien@example.com' => 'Fabien'));
    }

    // -- Creation Methods

    private function _createSendEvent(Swift_Mime_Message $message)
    {
        $evt = $this->getMockBuilder('Swift_Events_SendEvent')
                    ->disableOriginalConstructor()
                    ->getMock();
        $evt->expects($this->any())
            ->method('getMessage')
            ->will($this->returnValue($message));

        return $evt;
    }
}
