<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\ExceptionDataCollector;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExceptionDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testCollect()
    {
        $e = new \Exception('foo',500);
        $c = new ExceptionDataCollector();
        $flattened = FlattenException::create($e);
        $trace = $flattened->getTrace();

        $this->assertFalse($c->hasException());

        $c->collect(new Request(), new Response(),$e);

        $this->assertTrue($c->hasException());
        $this->assertEquals($flattened,$c->getException());
        $this->assertSame('foo',$c->getMessage());
        $this->assertSame(500,$c->getCode());
        $this->assertSame('exception',$c->getName());
        $this->assertSame($trace,$c->getTrace());
    }
}
