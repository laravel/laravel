<?php

class Swift_Mailer_ArrayRecipientIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testHasNextReturnsFalseForEmptyArray()
    {
        $it = new Swift_Mailer_ArrayRecipientIterator(array());
        $this->assertFalse($it->hasNext());
    }

    public function testHasNextReturnsTrueIfItemsLeft()
    {
        $it = new Swift_Mailer_ArrayRecipientIterator(array('foo@bar' => 'Foo'));
        $this->assertTrue($it->hasNext());
    }

    public function testReadingToEndOfListCausesHasNextToReturnFalse()
    {
        $it = new Swift_Mailer_ArrayRecipientIterator(array('foo@bar' => 'Foo'));
        $this->assertTrue($it->hasNext());
        $it->nextRecipient();
        $this->assertFalse($it->hasNext());
    }

    public function testReturnedValueHasPreservedKeyValuePair()
    {
        $it = new Swift_Mailer_ArrayRecipientIterator(array('foo@bar' => 'Foo'));
        $this->assertEquals(array('foo@bar' => 'Foo'), $it->nextRecipient());
    }

    public function testIteratorMovesNextAfterEachIteration()
    {
        $it = new Swift_Mailer_ArrayRecipientIterator(array(
            'foo@bar' => 'Foo',
            'zip@button' => 'Zip thing',
            'test@test' => null,
            ));
        $this->assertEquals(array('foo@bar' => 'Foo'), $it->nextRecipient());
        $this->assertEquals(array('zip@button' => 'Zip thing'), $it->nextRecipient());
        $this->assertEquals(array('test@test' => null), $it->nextRecipient());
    }
}
