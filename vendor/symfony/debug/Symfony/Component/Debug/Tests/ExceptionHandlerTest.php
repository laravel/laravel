<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug\Tests;

use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testDebug()
    {
        $handler = new ExceptionHandler(false);
        $response = $handler->createResponse(new \RuntimeException('Foo'));

        $this->assertContains('<h1>Whoops, looks like something went wrong.</h1>', $response->getContent());
        $this->assertNotContains('<div class="block_exception clear_fix">', $response->getContent());

        $handler = new ExceptionHandler(true);
        $response = $handler->createResponse(new \RuntimeException('Foo'));

        $this->assertContains('<h1>Whoops, looks like something went wrong.</h1>', $response->getContent());
        $this->assertContains('<div class="block_exception clear_fix">', $response->getContent());
    }

    public function testStatusCode()
    {
        $handler = new ExceptionHandler(false);

        $response = $handler->createResponse(new \RuntimeException('Foo'));
        $this->assertEquals('500', $response->getStatusCode());
        $this->assertContains('Whoops, looks like something went wrong.', $response->getContent());

        $response = $handler->createResponse(new NotFoundHttpException('Foo'));
        $this->assertEquals('404', $response->getStatusCode());
        $this->assertContains('Sorry, the page you are looking for could not be found.', $response->getContent());
    }

    public function testHeaders()
    {
        $handler = new ExceptionHandler(false);

        $response = $handler->createResponse(new MethodNotAllowedHttpException(array('POST')));
        $this->assertEquals('405', $response->getStatusCode());
        $this->assertEquals('POST', $response->headers->get('Allow'));
    }

    public function testNestedExceptions()
    {
        $handler = new ExceptionHandler(true);
        $response = $handler->createResponse(new \RuntimeException('Foo', 0, new \RuntimeException('Bar')));
    }
}
