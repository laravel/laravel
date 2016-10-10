<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops;
use Whoops\TestCase;
use Whoops\Run;
use Whoops\Handler\Handler;
use ArrayObject;
use Mockery as m;
use InvalidArgumentException;
use RuntimeException;
use Exception;

class RunTest extends TestCase
{

    /**
     * @param string $message
     * @return Exception
     */
    protected function getException($message = null)
    {
        return m::mock('Exception', array($message));
    }

    /**
     * @return Handler
     */
    protected function getHandler()
    {
        return m::mock('Whoops\\Handler\\Handler')
            ->shouldReceive('setRun')
                ->andReturn(null)
            ->mock()

            ->shouldReceive('setInspector')
                ->andReturn(null)
            ->mock()

            ->shouldReceive('setException')
                ->andReturn(null)
            ->mock()
        ;
    }

    /**
     * @covers Whoops\Run::clearHandlers
     */
    public function testClearHandlers()
    {
        $run = $this->getRunInstance();
        $run->clearHandlers();

        $handlers = $run->getHandlers();

        $this->assertEmpty($handlers);
    }

    /**
     * @covers Whoops\Run::pushHandler
     */
    public function testPushHandler()
    {
        $run = $this->getRunInstance();
        $run->clearHandlers();

        $handlerOne = $this->getHandler();
        $handlerTwo = $this->getHandler();

        $run->pushHandler($handlerOne);
        $run->pushHandler($handlerTwo);

        $handlers = $run->getHandlers();

        $this->assertCount(2, $handlers);
        $this->assertContains($handlerOne, $handlers);
        $this->assertContains($handlerTwo, $handlers);
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers Whoops\Run::pushHandler
     */
    public function testPushInvalidHandler()
    {
        $run = $this->getRunInstance();
        $run->pushHandler($banana = 'actually turnip');
    }

    /**
     * @covers Whoops\Run::pushHandler
     */
    public function testPushClosureBecomesHandler()
    {
        $run = $this->getRunInstance();
        $run->pushHandler(function() {});
        $this->assertInstanceOf('Whoops\\Handler\\CallbackHandler', $run->popHandler());
    }

    /**
     * @covers Whoops\Run::popHandler
     * @covers Whoops\Run::getHandlers
     */
    public function testPopHandler()
    {
        $run = $this->getRunInstance();

        $handlerOne   = $this->getHandler();
        $handlerTwo   = $this->getHandler();
        $handlerThree = $this->getHandler();

        $run->pushHandler($handlerOne);
        $run->pushHandler($handlerTwo);
        $run->pushHandler($handlerThree);

        $this->assertSame($handlerThree, $run->popHandler());
        $this->assertSame($handlerTwo, $run->popHandler());
        $this->assertSame($handlerOne, $run->popHandler());

        // Should return null if there's nothing else in
        // the stack
        $this->assertNull($run->popHandler());

        // Should be empty since we popped everything off
        // the stack:
        $this->assertEmpty($run->getHandlers());
    }

    /**
     * @covers Whoops\Run::register
     */
    public function testRegisterHandler()
    {
        // It is impossible to test the Run::register method using phpunit,
        // as given how every test is always inside a giant try/catch block,
        // any thrown exception will never hit a global exception handler.
        // On the other hand, there is not much need in testing
        // a call to a native PHP function.
        $this->assertTrue(true);
    }

    /**
     * @covers Whoops\Run::unregister
     * @expectedException Exception
     */
    public function testUnregisterHandler()
    {
        $run = $this->getRunInstance();
        $run->register();

        $handler = $this->getHandler();
        $run->pushHandler($handler);

        $run->unregister();
        throw $this->getException("I'm not supposed to be caught!");
    }

    /**
     * @covers Whoops\Run::pushHandler
     * @covers Whoops\Run::getHandlers
     */
    public function testHandlerHoldsOrder()
    {
        $run = $this->getRunInstance();

        $handlerOne   = $this->getHandler();
        $handlerTwo   = $this->getHandler();
        $handlerThree = $this->getHandler();
        $handlerFour  = $this->getHandler();

        $run->pushHandler($handlerOne);
        $run->pushHandler($handlerTwo);
        $run->pushHandler($handlerThree);
        $run->pushHandler($handlerFour);

        $handlers = $run->getHandlers();

        $this->assertSame($handlers[0], $handlerOne);
        $this->assertSame($handlers[1], $handlerTwo);
        $this->assertSame($handlers[2], $handlerThree);
        $this->assertSame($handlers[3], $handlerFour);
    }

    /**
     * @todo possibly split this up a bit and move
     *       some of this test to Handler unit tests?
     * @covers Whoops\Run::handleException
     */
    public function testHandlersGonnaHandle()
    {
        $run       = $this->getRunInstance();
        $exception = $this->getException();
        $order     = new ArrayObject;

        $handlerOne   = $this->getHandler();
        $handlerTwo   = $this->getHandler();
        $handlerThree = $this->getHandler();

        $handlerOne->shouldReceive('handle')
            ->andReturnUsing(function() use($order) { $order[] = 1; });
        $handlerTwo->shouldReceive('handle')
            ->andReturnUsing(function() use($order) { $order[] = 2; });
        $handlerThree->shouldReceive('handle')
            ->andReturnUsing(function() use($order) { $order[] = 3; });

        $run->pushHandler($handlerOne);
        $run->pushHandler($handlerTwo);
        $run->pushHandler($handlerThree);

        // Get an exception to be handled, and verify that the handlers
        // are given the handler, and in the inverse order they were
        // registered.
        $run->handleException($exception);
        $this->assertEquals((array) $order, array(3, 2, 1));
    }

    /**
     * @covers Whoops\Run::handleException
     */
    public function testLastHandler()
    {
        $run = $this->getRunInstance();

        $handlerOne = $this->getHandler();
        $handlerTwo = $this->getHandler();

        $run->pushHandler($handlerOne);
        $run->pushHandler($handlerTwo);

        $test = $this;
        $handlerOne
            ->shouldReceive('handle')
            ->andReturnUsing(function () use($test) {
                $test->fail('$handlerOne should not be called');
            })
        ;

        $handlerTwo
            ->shouldReceive('handle')
            ->andReturn(Handler::LAST_HANDLER)
        ;

        $run->handleException($this->getException());

        // Reached the end without errors
        $this->assertTrue(true);
    }

    /**
     * Test error suppression using @ operator.
     */
    public function testErrorSuppression()
    {
        $run = $this->getRunInstance();
        $run->register();

        $handler = $this->getHandler();
        $run->pushHandler($handler);

        $test = $this;
        $handler
            ->shouldReceive('handle')
            ->andReturnUsing(function () use($test) {
                $test->fail('$handler should not be called, error not suppressed');
            })
        ;

        @trigger_error("Test error suppression");

        // Reached the end without errors
        $this->assertTrue(true);
    }

    public function testErrorCatching()
    {
        $run = $this->getRunInstance();
        $run->register();

        $handler = $this->getHandler();
        $run->pushHandler($handler);

        $test = $this;
        $handler
            ->shouldReceive('handle')
            ->andReturnUsing(function () use($test) {
                $test->fail('$handler should not be called error should be caught');
            })
        ;

        try {
            trigger_error(E_USER_NOTICE, 'foo');
            $this->fail('Should not continue after error thrown');
        } catch (\ErrorException $e) {
            // Do nothing
            $this->assertTrue(true);
            return;
        }
        $this->fail('Should not continue here, should have been caught.');
    }

    /**
     * Test to make sure that error_reporting is respected.
     */
    public function testErrorReporting()
    {
        $run = $this->getRunInstance();
        $run->register();

        $handler = $this->getHandler();
        $run->pushHandler($handler);

        $test = $this;
        $handler
            ->shouldReceive('handle')
            ->andReturnUsing(function () use($test) {
                $test->fail('$handler should not be called, error_reporting not respected');
            })
        ;

        $oldLevel = error_reporting(E_ALL ^ E_USER_NOTICE);
        trigger_error("Test error reporting", E_USER_NOTICE);
        error_reporting($oldLevel);

        // Reached the end without errors
        $this->assertTrue(true);
    }

    /**
     * @covers Whoops\Run::silenceErrorsInPaths
     */
    public function testSilenceErrorsInPaths()
    {
        $run = $this->getRunInstance();
        $run->register();

        $handler = $this->getHandler();
        $run->pushHandler($handler);

        $test = $this;
        $handler
            ->shouldReceive('handle')
            ->andReturnUsing(function () use($test) {
                $test->fail('$handler should not be called, silenceErrorsInPaths not respected');
            })
        ;

        $run->silenceErrorsInPaths('@^'.preg_quote(__FILE__).'$@', E_USER_NOTICE);
        trigger_error('Test', E_USER_NOTICE);
        $this->assertTrue(true);
    }

    /**
     * @covers Whoops\Run::handleException
     * @covers Whoops\Run::writeToOutput
     */
    public function testOutputIsSent()
    {
        $run = $this->getRunInstance();
        $run->pushHandler(function() {
            echo "hello there";
        });

        ob_start();
        $run->handleException(new RuntimeException);
        $this->assertEquals("hello there", ob_get_clean());
    }

    /**
     * @covers Whoops\Run::handleException
     * @covers Whoops\Run::writeToOutput
     */
    public function testOutputIsNotSent()
    {
        $run = $this->getRunInstance();
        $run->writeToOutput(false);
        $run->pushHandler(function() {
            echo "hello there";
        });

        ob_start();
        $this->assertEquals("hello there", $run->handleException(new RuntimeException));
        $this->assertEquals("", ob_get_clean());
    }

    /**
     * @covers Whoops\Run::sendHttpCode
     */
    public function testSendHttpCode()
    {
        $run = $this->getRunInstance();
        $run->sendHttpCode(true);
        $this->assertEquals(500, $run->sendHttpCode());
    }

    /**
     * @covers Whoops\Run::sendHttpCode
     * @expectedException InvalidArgumentException
     */
    public function testSendHttpCodeWrongCode()
    {
        $this->getRunInstance()->sendHttpCode(1337);
    }
}
