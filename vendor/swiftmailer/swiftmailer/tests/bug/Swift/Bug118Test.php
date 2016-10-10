<?php

class Swift_Bug118Test extends \PHPUnit_Framework_TestCase
{
    private $_message;

    public function setUp()
    {
        $this->_message = new Swift_Message();
    }

    public function testCallingGenerateIdChangesTheMessageId()
    {
        $currentId = $this->_message->getId();
        $this->_message->generateId();
        $newId = $this->_message->getId();

        $this->assertNotEquals($currentId, $newId);
    }
}
