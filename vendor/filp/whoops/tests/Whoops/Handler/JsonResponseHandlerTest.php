<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;
use Whoops\TestCase;
use Whoops\Handler\JsonResponseHandler;
use RuntimeException;

class JsonResponseHandlerTest extends TestCase
{
    /**
     * @return Whoops\Handler\JsonResponseHandler
     */
    private function getHandler()
    {
        return new JsonResponseHandler;
    }

    /**
     * @return RuntimeException
     */
    public function getException($message = 'test message')
    {
        return new RuntimeException($message);
    }

    /**
     * @param  bool  $withTrace
     * @return array
     */
    private function getJsonResponseFromHandler($withTrace = false)
    {
        $handler = $this->getHandler();
        $handler->addTraceToOutput($withTrace);

        $run = $this->getRunInstance();
        $run->pushHandler($handler);
        $run->register();

        $exception = $this->getException();
        ob_start();
        $run->handleException($exception);
        $json = json_decode(ob_get_clean(), true);

        // Check that the json response is parse-able:
        $this->assertEquals(json_last_error(), JSON_ERROR_NONE);

        return $json;
    }

    /**
     * @covers Whoops\Handler\JsonResponseHandler::addTraceToOutput
     * @covers Whoops\Handler\JsonResponseHandler::handle
     */
    public function testReturnsWithoutFrames()
    {
        $json = $this->getJsonResponseFromHandler($withTrace = false);

        // Check that the response has the expected keys:
        $this->assertArrayHasKey('error', $json);
        $this->assertArrayHasKey('type', $json['error']);
        $this->assertArrayHasKey('file', $json['error']);
        $this->assertArrayHasKey('line', $json['error']);

        // Check the field values:
        $this->assertEquals($json['error']['file'], __FILE__);
        $this->assertEquals($json['error']['message'], 'test message');
        $this->assertEquals($json['error']['type'], get_class($this->getException()));

        // Check that the trace is NOT returned:
        $this->assertArrayNotHasKey('trace', $json['error']);
    }

    /**
     * @covers Whoops\Handler\JsonResponseHandler::addTraceToOutput
     * @covers Whoops\Handler\JsonResponseHandler::handle
     */
    public function testReturnsWithFrames()
    {
        $json = $this->getJsonResponseFromHandler($withTrace = true);

        // Check that the trace is returned:
        $this->assertArrayHasKey('trace', $json['error']);

        // Check that a random frame has the expected fields
        $traceFrame = reset($json['error']['trace']);
        $this->assertArrayHasKey('file', $traceFrame);
        $this->assertArrayHasKey('line', $traceFrame);
        $this->assertArrayHasKey('function', $traceFrame);
        $this->assertArrayHasKey('class', $traceFrame);
        $this->assertArrayHasKey('args', $traceFrame);
    }    
}
