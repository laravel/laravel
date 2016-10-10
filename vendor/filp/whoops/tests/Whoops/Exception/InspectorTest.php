<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;
use Whoops\Exception\Inspector;
use Whoops\TestCase;
use RuntimeException;
use Exception;
use Mockery as m;

class InspectorTest extends TestCase
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
     * @param  Exception $exception|null
     * @return Whoops\Exception\Inspector
     */
    protected function getInspectorInstance(Exception $exception = null)
    {
        return new Inspector($exception);
    }

    /**
     * @covers Whoops\Exception\Inspector::getExceptionName
     */
    public function testReturnsCorrectExceptionName()
    {
        $exception = $this->getException();
        $inspector = $this->getInspectorInstance($exception);

        $this->assertEquals(get_class($exception), $inspector->getExceptionName());
    }

    /**
     * @covers Whoops\Exception\Inspector::__construct
     * @covers Whoops\Exception\Inspector::getException
     */
    public function testExceptionIsStoredAndReturned()
    {
        $exception = $this->getException();
        $inspector = $this->getInspectorInstance($exception);

        $this->assertSame($exception, $inspector->getException());
    }

    /**
     * @covers Whoops\Exception\Inspector::getFrames
     */
    public function testGetFramesReturnsCollection()
    {
        $exception = $this->getException();
        $inspector = $this->getInspectorInstance($exception);

        $this->assertInstanceOf('Whoops\\Exception\\FrameCollection', $inspector->getFrames());
    }
}
