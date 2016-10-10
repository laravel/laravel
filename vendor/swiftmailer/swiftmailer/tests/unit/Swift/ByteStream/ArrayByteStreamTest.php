<?php

class Swift_ByteStream_ArrayByteStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testReadingSingleBytesFromBaseInput()
    {
        $input = array('a', 'b', 'c');
        $bs = $this->_createArrayStream($input);
        $output = array();
        while (false !== $bytes = $bs->read(1)) {
            $output[] = $bytes;
        }
        $this->assertEquals($input, $output,
            '%s: Bytes read from stream should be the same as bytes in constructor'
            );
    }

    public function testReadingMultipleBytesFromBaseInput()
    {
        $input = array('a', 'b', 'c', 'd');
        $bs = $this->_createArrayStream($input);
        $output = array();
        while (false !== $bytes = $bs->read(2)) {
            $output[] = $bytes;
        }
        $this->assertEquals(array('ab', 'cd'), $output,
            '%s: Bytes read from stream should be in pairs'
            );
    }

    public function testReadingOddOffsetOnLastByte()
    {
        $input = array('a', 'b', 'c', 'd', 'e');
        $bs = $this->_createArrayStream($input);
        $output = array();
        while (false !== $bytes = $bs->read(2)) {
            $output[] = $bytes;
        }
        $this->assertEquals(array('ab', 'cd', 'e'), $output,
            '%s: Bytes read from stream should be in pairs except final read'
            );
    }

    public function testSettingPointerPartway()
    {
        $input = array('a', 'b', 'c');
        $bs = $this->_createArrayStream($input);
        $bs->setReadPointer(1);
        $this->assertEquals('b', $bs->read(1),
            '%s: Byte should be second byte since pointer as at offset 1'
            );
    }

    public function testResettingPointerAfterExhaustion()
    {
        $input = array('a', 'b', 'c');
        $bs = $this->_createArrayStream($input);

        while (false !== $bs->read(1));

        $bs->setReadPointer(0);
        $this->assertEquals('a', $bs->read(1),
            '%s: Byte should be first byte since pointer as at offset 0'
            );
    }

    public function testPointerNeverSetsBelowZero()
    {
        $input = array('a', 'b', 'c');
        $bs = $this->_createArrayStream($input);

        $bs->setReadPointer(-1);
        $this->assertEquals('a', $bs->read(1),
            '%s: Byte should be first byte since pointer should be at offset 0'
            );
    }

    public function testPointerNeverSetsAboveStackSize()
    {
        $input = array('a', 'b', 'c');
        $bs = $this->_createArrayStream($input);

        $bs->setReadPointer(3);
        $this->assertSame(false, $bs->read(1),
            '%s: Stream should be at end and thus return false'
            );
    }

    public function testBytesCanBeWrittenToStream()
    {
        $input = array('a', 'b', 'c');
        $bs = $this->_createArrayStream($input);

        $bs->write('de');

        $output = array();
        while (false !== $bytes = $bs->read(1)) {
            $output[] = $bytes;
        }
        $this->assertEquals(array('a', 'b', 'c', 'd', 'e'), $output,
            '%s: Bytes read from stream should be from initial stack + written'
            );
    }

    public function testContentsCanBeFlushed()
    {
        $input = array('a', 'b', 'c');
        $bs = $this->_createArrayStream($input);

        $bs->flushBuffers();

        $this->assertSame(false, $bs->read(1),
            '%s: Contents have been flushed so read() should return false'
            );
    }

    public function testConstructorCanTakeStringArgument()
    {
        $bs = $this->_createArrayStream('abc');
        $output = array();
        while (false !== $bytes = $bs->read(1)) {
            $output[] = $bytes;
        }
        $this->assertEquals(array('a', 'b', 'c'), $output,
            '%s: Bytes read from stream should be the same as bytes in constructor'
            );
    }

    public function testBindingOtherStreamsMirrorsWriteOperations()
    {
        $bs = $this->_createArrayStream('');
        $is1 = $this->getMock('Swift_InputByteStream');
        $is2 = $this->getMock('Swift_InputByteStream');

        $is1->expects($this->at(0))
            ->method('write')
            ->with('x');
        $is1->expects($this->at(1))
            ->method('write')
            ->with('y');
        $is2->expects($this->at(0))
            ->method('write')
            ->with('x');
        $is2->expects($this->at(1))
            ->method('write')
            ->with('y');

        $bs->bind($is1);
        $bs->bind($is2);

        $bs->write('x');
        $bs->write('y');
    }

    public function testBindingOtherStreamsMirrorsFlushOperations()
    {
        $bs = $this->_createArrayStream('');
        $is1 = $this->getMock('Swift_InputByteStream');
        $is2 = $this->getMock('Swift_InputByteStream');

        $is1->expects($this->once())
            ->method('flushBuffers');
        $is2->expects($this->once())
            ->method('flushBuffers');

        $bs->bind($is1);
        $bs->bind($is2);

        $bs->flushBuffers();
    }

    public function testUnbindingStreamPreventsFurtherWrites()
    {
        $bs = $this->_createArrayStream('');
        $is1 = $this->getMock('Swift_InputByteStream');
        $is2 = $this->getMock('Swift_InputByteStream');

        $is1->expects($this->at(0))
            ->method('write')
            ->with('x');
        $is1->expects($this->at(1))
            ->method('write')
            ->with('y');
        $is2->expects($this->once())
            ->method('write')
            ->with('x');

        $bs->bind($is1);
        $bs->bind($is2);

        $bs->write('x');

        $bs->unbind($is2);

        $bs->write('y');
    }

    // -- Creation Methods

    private function _createArrayStream($input)
    {
        return new Swift_ByteStream_ArrayByteStream($input);
    }
}
