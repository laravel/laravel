<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;

use InvalidArgumentException;

/**
 * Wrapper for Closures passed as handlers. Can be used
 * directly, or will be instantiated automagically by Whoops\Run
 * if passed to Run::pushHandler
 */
class CallbackHandler extends Handler
{
    /**
     * @var callable
     */
    protected $callable;

    /**
     * @throws InvalidArgumentException If argument is not callable
     * @param  callable                 $callable
     */
    public function __construct($callable)
    {
        if (!is_callable($callable)) {
            throw new InvalidArgumentException(
                'Argument to ' . __METHOD__ . ' must be valid callable'
            );
        }

        $this->callable = $callable;
    }

    /**
     * @return int|null
     */
    public function handle()
    {
        $exception = $this->getException();
        $inspector = $this->getInspector();
        $run       = $this->getRun();
        $callable  = $this->callable;

        // invoke the callable directly, to get simpler stacktraces (in comparison to call_user_func).
        // this assumes that $callable is a properly typed php-callable, which we check in __construct().
        return $callable($exception, $inspector, $run);
    }
}
