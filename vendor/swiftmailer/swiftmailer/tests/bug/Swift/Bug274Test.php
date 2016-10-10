<?php

class Swift_Bug274Test extends \PHPUnit_Framework_TestCase
{
    public function testEmptyFileNameAsAttachement()
    {
        $message = new Swift_Message();
        $this->setExpectedException('Swift_IoException', 'The path cannot be empty');
        $message->attach(Swift_Attachment::fromPath(''));
    }

    public function testNonEmptyFileNameAsAttachement()
    {
        $message = new Swift_Message();
        try {
            $message->attach(Swift_Attachment::fromPath(__FILE__));
        } catch (Exception $e) {
            $this->fail('Path should not be empty');
        }
    }
}
