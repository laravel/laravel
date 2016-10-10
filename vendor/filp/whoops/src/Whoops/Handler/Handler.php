<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;
use Whoops\Handler\HandlerInterface;
use Whoops\Exception\Inspector;
use Whoops\Run;
use Exception;

/**
 * Abstract implementation of a Handler.
 */
abstract class Handler implements HandlerInterface
{
    /**
     * Return constants that can be returned from Handler::handle
     * to message the handler walker.
     */
    const DONE         = 0x10; // returning this is optional, only exists for
                               // semantic purposes
    const LAST_HANDLER = 0x20;
    const QUIT         = 0x30;

    /**
     * @var Run
     */
    private $run;

    /**
     * @var Inspector $inspector
     */
    private $inspector;

    /**
     * @var Exception $exception
     */
    private $exception;

    /**
     * @param Run $run
     */
    public function setRun(Run $run)
    {
        $this->run = $run;
    }

    /**
     * @return Run
     */
    protected function getRun()
    {
        return $this->run;
    }

    /**
     * @param Inspector $inspector
     */
    public function setInspector(Inspector $inspector)
    {
        $this->inspector = $inspector;
    }

    /**
     * @return Inspector
     */
    protected function getInspector()
    {
        return $this->inspector;
    }

    /**
     * @param Exception $exception
     */
    public function setException(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return Exception
     */
    protected function getException()
    {
        return $this->exception;
    }
}
