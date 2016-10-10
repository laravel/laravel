<?php

class PHPParser_Tests_Node_NameTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct() {
        $name = new PHPParser_Node_Name(array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $name->parts);

        $name = new PHPParser_Node_Name('foo\bar');
        $this->assertEquals(array('foo', 'bar'), $name->parts);
    }

    public function testGet() {
        $name = new PHPParser_Node_Name('foo');
        $this->assertEquals('foo', $name->getFirst());
        $this->assertEquals('foo', $name->getLast());

        $name = new PHPParser_Node_Name('foo\bar');
        $this->assertEquals('foo', $name->getFirst());
        $this->assertEquals('bar', $name->getLast());
    }

    public function testToString() {
        $name = new PHPParser_Node_Name('foo\bar');

        $this->assertEquals('foo\bar', (string) $name);
        $this->assertEquals('foo\bar', $name->toString());
        $this->assertEquals('foo_bar', $name->toString('_'));
    }

    public function testSet() {
        $name = new PHPParser_Node_Name('foo');

        $name->set('foo\bar');
        $this->assertEquals('foo\bar', $name->toString());

        $name->set(array('foo', 'bar'));
        $this->assertEquals('foo\bar', $name->toString());

        $name->set(new PHPParser_Node_Name('foo\bar'));
        $this->assertEquals('foo\bar', $name->toString());
    }

    public function testSetFirst() {
        $name = new PHPParser_Node_Name('foo');

        $name->setFirst('bar');
        $this->assertEquals('bar', $name->toString());

        $name->setFirst('A\B');
        $this->assertEquals('A\B', $name->toString());

        $name->setFirst('C');
        $this->assertEquals('C\B', $name->toString());

        $name->setFirst('D\E');
        $this->assertEquals('D\E\B', $name->toString());
    }

    public function testSetLast() {
        $name = new PHPParser_Node_Name('foo');

        $name->setLast('bar');
        $this->assertEquals('bar', $name->toString());

        $name->setLast('A\B');
        $this->assertEquals('A\B', $name->toString());

        $name->setLast('C');
        $this->assertEquals('A\C', $name->toString());

        $name->setLast('D\E');
        $this->assertEquals('A\D\E', $name->toString());
    }

    public function testAppend() {
        $name = new PHPParser_Node_Name('foo');

        $name->append('bar');
        $this->assertEquals('foo\bar', $name->toString());

        $name->append('bar\foo');
        $this->assertEquals('foo\bar\bar\foo', $name->toString());
    }

    public function testPrepend() {
        $name = new PHPParser_Node_Name('foo');

        $name->prepend('bar');
        $this->assertEquals('bar\foo', $name->toString());

        $name->prepend('foo\bar');
        $this->assertEquals('foo\bar\bar\foo', $name->toString());
    }

    public function testIs() {
        $name = new PHPParser_Node_Name('foo');
        $this->assertTrue ($name->isUnqualified());
        $this->assertFalse($name->isQualified());
        $this->assertFalse($name->isFullyQualified());
        $this->assertFalse($name->isRelative());

        $name = new PHPParser_Node_Name('foo\bar');
        $this->assertFalse($name->isUnqualified());
        $this->assertTrue ($name->isQualified());
        $this->assertFalse($name->isFullyQualified());
        $this->assertFalse($name->isRelative());

        $name = new PHPParser_Node_Name_FullyQualified('foo');
        $this->assertFalse($name->isUnqualified());
        $this->assertFalse($name->isQualified());
        $this->assertTrue ($name->isFullyQualified());
        $this->assertFalse($name->isRelative());

        $name = new PHPParser_Node_Name_Relative('foo');
        $this->assertFalse($name->isUnqualified());
        $this->assertFalse($name->isQualified());
        $this->assertFalse($name->isFullyQualified());
        $this->assertTrue ($name->isRelative());
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage When changing a name you need to pass either a string, an array or a Name node
     */
    public function testInvalidArg() {
        $name = new PHPParser_Node_Name('foo');
        $name->set(new stdClass);
    }
}