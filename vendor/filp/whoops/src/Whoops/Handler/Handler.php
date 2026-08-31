<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;

use Whoops\Inspector\InspectorInterface;
use Whoops\RunInterface;

/**
 * Abstract implementation of a Handler.
 */
abstract class Handler implements HandlerInterface
{
    /*
     Return constants that can be returned from Handler::handle
     to message the handler walker.
     */
    const DONE         = 0x10; // returning this is optional, only exists for
                               // semantic purposes
    /**
     * The Handler has handled the Throwable in some way, and wishes to skip any other Handler.
     * Execution will continue.
     */
    const LAST_HANDLER = 0x20;
    /**
     * The Handler has handled the Throwable in some way, and wishes to quit/stop execution
     */
    const QUIT         = 0x30;

    /**
     * @var RunInterface
     */
    private $run;

    /**
     * @var InspectorInterface $inspector
     */
    private $inspector;

    /**
     * @var \Throwable $exception
     */
    private $exception;

    /**
     * @param RunInterface $run
     */
    public function setRun(RunInterface $run)
    {
        $this->run = $run;
    }

    /**
     * @return RunInterface
     */
    protected function getRun()
    {
        return $this->run;
    }

    /**
     * @param InspectorInterface $inspector
     */
    public function setInspector(InspectorInterface $inspector)
    {
        $this->inspector = $inspector;
    }

    /**
     * @return InspectorInterface
     */
    protected function getInspector()
    {
        return $this->inspector;
    }

    /**
     * @param \Throwable $exception
     */
    public function setException($exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return \Throwable
     */
    protected function getException()
    {
        return $this->exception;
    }
}
