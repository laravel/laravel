<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Tests\Parser;

use Symfony\Component\CssSelector\Parser\Reader;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testIsEOF()
    {
        $reader = new Reader('');
        $this->assertTrue($reader->isEOF());

        $reader = new Reader('hello');
        $this->assertFalse($reader->isEOF());

        $this->assignPosition($reader, 2);
        $this->assertFalse($reader->isEOF());

        $this->assignPosition($reader, 5);
        $this->assertTrue($reader->isEOF());
    }

    public function testGetRemainingLength()
    {
        $reader = new Reader('hello');
        $this->assertEquals(5, $reader->getRemainingLength());

        $this->assignPosition($reader, 2);
        $this->assertEquals(3, $reader->getRemainingLength());

        $this->assignPosition($reader, 5);
        $this->assertEquals(0, $reader->getRemainingLength());
    }

    public function testGetSubstring()
    {
        $reader = new Reader('hello');
        $this->assertEquals('he', $reader->getSubstring(2));
        $this->assertEquals('el', $reader->getSubstring(2, 1));

        $this->assignPosition($reader, 2);
        $this->assertEquals('ll', $reader->getSubstring(2));
        $this->assertEquals('lo', $reader->getSubstring(2, 1));
    }

    public function testGetOffset()
    {
        $reader = new Reader('hello');
        $this->assertEquals(2, $reader->getOffset('ll'));
        $this->assertFalse($reader->getOffset('w'));

        $this->assignPosition($reader, 2);
        $this->assertEquals(0, $reader->getOffset('ll'));
        $this->assertFalse($reader->getOffset('he'));
    }

    public function testFindPattern()
    {
        $reader = new Reader('hello');

        $this->assertFalse($reader->findPattern('/world/'));
        $this->assertEquals(array('hello', 'h'), $reader->findPattern('/^([a-z]).*/'));

        $this->assignPosition($reader, 2);
        $this->assertFalse($reader->findPattern('/^h.*/'));
        $this->assertEquals(array('llo'), $reader->findPattern('/^llo$/'));
    }

    public function testMoveForward()
    {
        $reader = new Reader('hello');
        $this->assertEquals(0, $reader->getPosition());

        $reader->moveForward(2);
        $this->assertEquals(2, $reader->getPosition());
    }

    public function testToEnd()
    {
        $reader = new Reader('hello');
        $reader->moveToEnd();
        $this->assertTrue($reader->isEOF());
    }

    private function assignPosition(Reader $reader, $value)
    {
        $position = new \ReflectionProperty($reader, 'position');
        $position->setAccessible(true);
        $position->setValue($reader, $value);
    }
}
