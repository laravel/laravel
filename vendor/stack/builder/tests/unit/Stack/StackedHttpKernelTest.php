<?php

namespace Stack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

class StackedHttpKernelTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function handleShouldDelegateToApp()
    {
        $app = $this->getHttpKernelMock(new Response('ok'));
        $kernel = new StackedHttpKernel($app, array($app));

        $request = Request::create('/');
        $response = $kernel->handle($request);

        $this->assertSame('ok', $response->getContent());
    }

    /** @test */
    public function handleShouldStillDelegateToAppWithMiddlewares()
    {
        $app = $this->getHttpKernelMock(new Response('ok'));
        $bar = $this->getHttpKernelMock(new Response('bar'));
        $foo = $this->getHttpKernelMock(new Response('foo'));
        $kernel = new StackedHttpKernel($app, array($foo, $bar, $app));

        $request = Request::create('/');
        $response = $kernel->handle($request);

        $this->assertSame('ok', $response->getContent());
    }

    /** @test */
    public function terminateShouldDelegateToMiddlewares()
    {
        $first  = new TerminableKernelSpy();
        $second = new TerminableKernelSpy($first);
        $third  = new KernelSpy($second);
        $fourth = new TerminableKernelSpy($third);
        $fifth  = new TerminableKernelSpy($fourth);

        $kernel = new StackedHttpKernel($fifth, $middlewares = array($fifth, $fourth, $third, $second, $first));

        $request = Request::create('/');
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);

        $this->assertTerminablesCalledOnce($middlewares);
    }

    private function assertTerminablesCalledOnce(array $middlewares)
    {
        foreach ($middlewares as $kernel) {
            if ($kernel instanceof TerminableInterface) {
                $this->assertEquals(1, $kernel->terminateCallCount(), "Terminate was called {$kernel->terminateCallCount()} times");
            }
        }
    }

    private function getHttpKernelMock(Response $response)
    {
        $app = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $app->expects($this->any())
            ->method('handle')
            ->with($this->isInstanceOf('Symfony\Component\HttpFoundation\Request'))
            ->will($this->returnValue($response));

        return $app;
    }

    private function getTerminableMock(Response $response = null)
    {
        $app = $this->getMock('Stack\TerminableHttpKernel');
        if ($response) {
            $app->expects($this->any())
                ->method('handle')
                ->with($this->isInstanceOf('Symfony\Component\HttpFoundation\Request'))
                ->will($this->returnValue($response));
        }
        $app->expects($this->once())
            ->method('terminate')
            ->with(
                $this->isInstanceOf('Symfony\Component\HttpFoundation\Request'),
                $this->isInstanceOf('Symfony\Component\HttpFoundation\Response')
            );

        return $app;
    }

    private function getDelegatingTerminableMock(TerminableInterface $next)
    {
        $app = $this->getMock('Stack\TerminableHttpKernel');
        $app->expects($this->once())
            ->method('terminate')
            ->with(
                $this->isInstanceOf('Symfony\Component\HttpFoundation\Request'),
                $this->isInstanceOf('Symfony\Component\HttpFoundation\Response')
            )
            ->will($this->returnCallback(function ($request, $response) use ($next) {
                $next->terminate($request, $response);
            }));

        return $app;
    }
}

class KernelSpy implements HttpKernelInterface
{
    private $handleCallCount = 0;

    public function __construct(HttpKernelInterface $kernel = null)
    {
        $this->kernel = $kernel;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $this->handleCallCount++;

        if ($this->kernel) {
            return $this->kernel->handle($request, $type, $catch);
        }

        return new Response('OK');
    }

    public function handleCallCount()
    {
        return $this->handleCallCount;
    }
}

class TerminableKernelSpy extends KernelSpy implements TerminableInterface
{
    private $terminateCallCount = 0;

    public function terminate(Request $request, Response $response)
    {
        $this->terminateCallCount++;

        if ($this->kernel && $this->kernel instanceof TerminableInterface) {
            return $this->kernel->terminate($request, $response);
        }
    }

    public function terminateCallCount()
    {
        return $this->terminateCallCount;
    }
}
