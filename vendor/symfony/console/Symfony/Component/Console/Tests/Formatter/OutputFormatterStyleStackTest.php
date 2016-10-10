<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Formatter;

use Symfony\Component\Console\Formatter\OutputFormatterStyleStack;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class OutputFormatterStyleStackTest extends \PHPUnit_Framework_TestCase
{
    public function testPush()
    {
        $stack = new OutputFormatterStyleStack();
        $stack->push($s1 = new OutputFormatterStyle('white', 'black'));
        $stack->push($s2 = new OutputFormatterStyle('yellow', 'blue'));

        $this->assertEquals($s2, $stack->getCurrent());

        $stack->push($s3 = new OutputFormatterStyle('green', 'red'));

        $this->assertEquals($s3, $stack->getCurrent());
    }

    public function testPop()
    {
        $stack = new OutputFormatterStyleStack();
        $stack->push($s1 = new OutputFormatterStyle('white', 'black'));
        $stack->push($s2 = new OutputFormatterStyle('yellow', 'blue'));

        $this->assertEquals($s2, $stack->pop());
        $this->assertEquals($s1, $stack->pop());
    }

    public function testPopEmpty()
    {
        $stack = new OutputFormatterStyleStack();
        $style = new OutputFormatterStyle();

        $this->assertEquals($style, $stack->pop());
    }

    public function testPopNotLast()
    {
        $stack = new OutputFormatterStyleStack();
        $stack->push($s1 = new OutputFormatterStyle('white', 'black'));
        $stack->push($s2 = new OutputFormatterStyle('yellow', 'blue'));
        $stack->push($s3 = new OutputFormatterStyle('green', 'red'));

        $this->assertEquals($s2, $stack->pop($s2));
        $this->assertEquals($s1, $stack->pop());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPop()
    {
        $stack = new OutputFormatterStyleStack();
        $stack->push(new OutputFormatterStyle('white', 'black'));
        $stack->pop(new OutputFormatterStyle('yellow', 'blue'));
    }
}
