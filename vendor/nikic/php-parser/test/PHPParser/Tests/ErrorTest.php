<?php

class PHPParser_Tests_ErrorTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct() {
        $error = new PHPParser_Error('Some error', 10);

        $this->assertEquals('Some error', $error->getRawMessage());
        $this->assertEquals(10, $error->getRawLine());
        $this->assertEquals('Some error on line 10', $error->getMessage());

        return $error;
    }

    /**
     * @depends testConstruct
     */
    public function testSetMessageAndLine(PHPParser_Error $error) {
        $error->setRawMessage('Some other error');
        $error->setRawLine(15);

        $this->assertEquals('Some other error', $error->getRawMessage());
        $this->assertEquals(15, $error->getRawLine());
        $this->assertEquals('Some other error on line 15', $error->getMessage());
    }

    public function testUnknownLine() {
        $error = new PHPParser_Error('Some error');

        $this->assertEquals(-1, $error->getRawLine());
        $this->assertEquals('Some error on unknown line', $error->getMessage());
    }
}