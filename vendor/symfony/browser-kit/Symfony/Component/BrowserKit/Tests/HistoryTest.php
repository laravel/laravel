<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\BrowserKit\Tests;

use Symfony\Component\BrowserKit\History;
use Symfony\Component\BrowserKit\Request;

class HistoryTest extends \PHPUnit_Framework_TestCase
{
    public function testAdd()
    {
        $history = new History();
        $history->add(new Request('http://www.example1.com/', 'get'));
        $this->assertSame('http://www.example1.com/', $history->current()->getUri(), '->add() adds a request to the history');

        $history->add(new Request('http://www.example2.com/', 'get'));
        $this->assertSame('http://www.example2.com/', $history->current()->getUri(), '->add() adds a request to the history');

        $history->add(new Request('http://www.example3.com/', 'get'));
        $history->back();
        $history->add(new Request('http://www.example4.com/', 'get'));
        $this->assertSame('http://www.example4.com/', $history->current()->getUri(), '->add() adds a request to the history');

        $history->back();
        $this->assertSame('http://www.example2.com/', $history->current()->getUri(), '->add() adds a request to the history');
    }

    public function testClearIsEmpty()
    {
        $history = new History();
        $history->add(new Request('http://www.example.com/', 'get'));

        $this->assertFalse($history->isEmpty(), '->isEmpty() returns false if the history is not empty');

        $history->clear();

        $this->assertTrue($history->isEmpty(), '->isEmpty() true if the history is empty');
    }

    public function testCurrent()
    {
        $history = new History();

        try {
            $history->current();
            $this->fail('->current() throws a \LogicException if the history is empty');
        } catch (\Exception $e) {
            $this->assertInstanceof('LogicException', $e, '->current() throws a \LogicException if the history is empty');
        }

        $history->add(new Request('http://www.example.com/', 'get'));

        $this->assertSame('http://www.example.com/', $history->current()->getUri(), '->current() returns the current request in the history');
    }

    public function testBack()
    {
        $history = new History();
        $history->add(new Request('http://www.example.com/', 'get'));

        try {
            $history->back();
            $this->fail('->back() throws a \LogicException if the history is already on the first page');
        } catch (\Exception $e) {
            $this->assertInstanceof('LogicException', $e, '->current() throws a \LogicException if the history is already on the first page');
        }

        $history->add(new Request('http://www.example1.com/', 'get'));
        $history->back();

        $this->assertSame('http://www.example.com/', $history->current()->getUri(), '->back() returns the previous request in the history');
    }

    public function testForward()
    {
        $history = new History();
        $history->add(new Request('http://www.example.com/', 'get'));
        $history->add(new Request('http://www.example1.com/', 'get'));

        try {
            $history->forward();
            $this->fail('->forward() throws a \LogicException if the history is already on the last page');
        } catch (\Exception $e) {
            $this->assertInstanceof('LogicException', $e, '->forward() throws a \LogicException if the history is already on the last page');
        }

        $history->back();
        $history->forward();

        $this->assertSame('http://www.example1.com/', $history->current()->getUri(), '->forward() returns the next request in the history');
    }
}
