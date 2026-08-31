<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;

use Whoops\Inspector\InspectorFactory;
use Whoops\Inspector\InspectorInterface;
use Whoops\Util\Misc;

class Inspector implements InspectorInterface
{
    /**
     * @var \Throwable
     */
    private $exception;

    /**
     * @var \Whoops\Exception\FrameCollection
     */
    private $frames;

    /**
     * @var \Whoops\Exception\Inspector
     */
    private $previousExceptionInspector;

    /**
     * @var \Throwable[]
     */
    private $previousExceptions;

    /**
     * @var \Whoops\Inspector\InspectorFactoryInterface|null
     */
    protected $inspectorFactory;

    /**
     * @param \Throwable $exception The exception to inspect
     * @param \Whoops\Inspector\InspectorFactoryInterface $factory
     */
    public function __construct($exception, $factory = null)
    {
        $this->exception = $exception;
        $this->inspectorFactory = $factory ?: new InspectorFactory();
    }

    /**
     * @return \Throwable
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return string
     */
    public function getExceptionName()
    {
        return get_class($this->exception);
    }

    /**
     * @return string
     */
    public function getExceptionMessage()
    {
        return $this->extractDocrefUrl($this->exception->getMessage())['message'];
    }

    /**
     * @return string[]
     */
    public function getPreviousExceptionMessages()
    {
        return array_map(function ($prev) {
            /** @var \Throwable $prev */
            return $this->extractDocrefUrl($prev->getMessage())['message'];
        }, $this->getPreviousExceptions());
    }

    /**
     * @return int[]
     */
    public function getPreviousExceptionCodes()
    {
        return array_map(function ($prev) {
            /** @var \Throwable $prev */
            return $prev->getCode();
        }, $this->getPreviousExceptions());
    }

    /**
     * Returns a url to the php-manual related to the underlying error - when available.
     *
     * @return string|null
     */
    public function getExceptionDocrefUrl()
    {
        return $this->extractDocrefUrl($this->exception->getMessage())['url'];
    }

    private function extractDocrefUrl($message)
    {
        $docref = [
            'message' => $message,
            'url' => null,
        ];

        // php embbeds urls to the manual into the Exception message with the following ini-settings defined
        // http://php.net/manual/en/errorfunc.configuration.php#ini.docref-root
        if (!ini_get('html_errors') || !ini_get('docref_root')) {
            return $docref;
        }

        $pattern = "/\[<a href='([^']+)'>(?:[^<]+)<\/a>\]/";
        if (preg_match($pattern, $message, $matches)) {
            // -> strip those automatically generated links from the exception message
            $docref['message'] = preg_replace($pattern, '', $message, 1);
            $docref['url'] = $matches[1];
        }

        return $docref;
    }

    /**
     * Does the wrapped Exception has a previous Exception?
     * @return bool
     */
    public function hasPreviousException()
    {
        return $this->previousExceptionInspector || $this->exception->getPrevious();
    }

    /**
     * Returns an Inspector for a previous Exception, if any.
     * @todo   Clean this up a bit, cache stuff a bit better.
     * @return Inspector
     */
    public function getPreviousExceptionInspector()
    {
        if ($this->previousExceptionInspector === null) {
            $previousException = $this->exception->getPrevious();

            if ($previousException) {
                $this->previousExceptionInspector = $this->inspectorFactory->create($previousException);
            }
        }

        return $this->previousExceptionInspector;
    }


    /**
     * Returns an array of all previous exceptions for this inspector's exception
     * @return \Throwable[]
     */
    public function getPreviousExceptions()
    {
        if ($this->previousExceptions === null) {
            $this->previousExceptions = [];

            $prev = $this->exception->getPrevious();
            while ($prev !== null) {
                $this->previousExceptions[] = $prev;
                $prev = $prev->getPrevious();
            }
        }

        return $this->previousExceptions;
    }

    /**
     * Returns an iterator for the inspected exception's
     * frames.
     * 
     * @param array<callable> $frameFilters
     * 
     * @return \Whoops\Exception\FrameCollection
     */
    public function getFrames(array $frameFilters = [])
    {
        if ($this->frames === null) {
            $frames = $this->getTrace($this->exception);

            // Fill empty line/file info for call_user_func_array usages (PHP Bug #44428)
            foreach ($frames as $k => $frame) {
                if (empty($frame['file'])) {
                    // Default values when file and line are missing
                    $file = '[internal]';
                    $line = 0;

                    $next_frame = !empty($frames[$k + 1]) ? $frames[$k + 1] : [];

                    if ($this->isValidNextFrame($next_frame)) {
                        $file = $next_frame['file'];
                        $line = $next_frame['line'];
                    }

                    $frames[$k]['file'] = $file;
                    $frames[$k]['line'] = $line;
                }
            }

            // Find latest non-error handling frame index ($i) used to remove error handling frames
            $i = 0;
            foreach ($frames as $k => $frame) {
                if ($frame['file'] == $this->exception->getFile() && $frame['line'] == $this->exception->getLine()) {
                    $i = $k;
                }
            }

            // Remove error handling frames
            if ($i > 0) {
                array_splice($frames, 0, $i);
            }

            $firstFrame = $this->getFrameFromException($this->exception);
            array_unshift($frames, $firstFrame);

            $this->frames = new FrameCollection($frames);

            if ($previousInspector = $this->getPreviousExceptionInspector()) {
                // Keep outer frame on top of the inner one
                $outerFrames = $this->frames;
                $newFrames = clone $previousInspector->getFrames();
                // I assume it will always be set, but let's be safe
                if (isset($newFrames[0])) {
                    $newFrames[0]->addComment(
                        $previousInspector->getExceptionMessage(),
                        'Exception message:'
                    );
                }
                $newFrames->prependFrames($outerFrames->topDiff($newFrames));
                $this->frames = $newFrames;
            }

            // Apply frame filters callbacks on the frames stack
            if (!empty($frameFilters)) {
                foreach ($frameFilters as $filterCallback) {
                    $this->frames->filter($filterCallback);
                }
            }
        }

        return $this->frames;
    }

    /**
     * Gets the backtrace from an exception.
     *
     * If xdebug is installed
     *
     * @param \Throwable $e
     * @return array
     */
    protected function getTrace($e)
    {
        $traces = $e->getTrace();

        // Get trace from xdebug if enabled, failure exceptions only trace to the shutdown handler by default
        if (!$e instanceof \ErrorException) {
            return $traces;
        }

        if (!Misc::isLevelFatal($e->getSeverity())) {
            return $traces;
        }

        if (!extension_loaded('xdebug') || !function_exists('xdebug_is_enabled') || !xdebug_is_enabled()) {
            return $traces;
        }

        // Use xdebug to get the full stack trace and remove the shutdown handler stack trace
        $stack = array_reverse(xdebug_get_function_stack());
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $traces = array_diff_key($stack, $trace);

        return $traces;
    }

    /**
     * Given an exception, generates an array in the format
     * generated by Exception::getTrace()
     * @param  \Throwable $exception
     * @return array
     */
    protected function getFrameFromException($exception)
    {
        return [
            'file'  => $exception->getFile(),
            'line'  => $exception->getLine(),
            'class' => get_class($exception),
            'args'  => [
                $exception->getMessage(),
            ],
        ];
    }

    /**
     * Given an error, generates an array in the format
     * generated by ErrorException
     * @param  ErrorException $exception
     * @return array
     */
    protected function getFrameFromError(ErrorException $exception)
    {
        return [
            'file'  => $exception->getFile(),
            'line'  => $exception->getLine(),
            'class' => null,
            'args'  => [],
        ];
    }

    /**
     * Determine if the frame can be used to fill in previous frame's missing info
     * happens for call_user_func and call_user_func_array usages (PHP Bug #44428)
     *
     * @return bool
     */
    protected function isValidNextFrame(array $frame)
    {
        if (empty($frame['file'])) {
            return false;
        }

        if (empty($frame['line'])) {
            return false;
        }

        if (empty($frame['function']) || !stristr($frame['function'], 'call_user_func')) {
            return false;
        }

        return true;
    }
}
