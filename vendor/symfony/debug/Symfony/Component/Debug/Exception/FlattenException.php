<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug\Exception;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * FlattenException wraps a PHP Exception to be able to serialize it.
 *
 * Basically, this class removes all objects from the trace.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FlattenException
{
    private $message;
    private $code;
    private $previous;
    private $trace;
    private $class;
    private $statusCode;
    private $headers;
    private $file;
    private $line;

    public static function create(\Exception $exception, $statusCode = null, array $headers = array())
    {
        $e = new static();
        $e->setMessage($exception->getMessage());
        $e->setCode($exception->getCode());

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $headers = array_merge($headers, $exception->getHeaders());
        }

        if (null === $statusCode) {
            $statusCode = 500;
        }

        $e->setStatusCode($statusCode);
        $e->setHeaders($headers);
        $e->setTraceFromException($exception);
        $e->setClass(get_class($exception));
        $e->setFile($exception->getFile());
        $e->setLine($exception->getLine());
        if ($exception->getPrevious()) {
            $e->setPrevious(static::create($exception->getPrevious()));
        }

        return $e;
    }

    public function toArray()
    {
        $exceptions = array();
        foreach (array_merge(array($this), $this->getAllPrevious()) as $exception) {
            $exceptions[] = array(
                'message' => $exception->getMessage(),
                'class'   => $exception->getClass(),
                'trace'   => $exception->getTrace(),
            );
        }

        return $exceptions;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($code)
    {
        $this->statusCode = $code;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setClass($class)
    {
        $this->class = $class;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function setLine($line)
    {
        $this->line = $line;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getPrevious()
    {
        return $this->previous;
    }

    public function setPrevious(FlattenException $previous)
    {
        $this->previous = $previous;
    }

    public function getAllPrevious()
    {
        $exceptions = array();
        $e = $this;
        while ($e = $e->getPrevious()) {
            $exceptions[] = $e;
        }

        return $exceptions;
    }

    public function getTrace()
    {
        return $this->trace;
    }

    public function setTraceFromException(\Exception $exception)
    {
        $trace = $exception->getTrace();

        if ($exception instanceof FatalErrorException) {
            if (function_exists('xdebug_get_function_stack')) {
                $trace = array_slice(array_reverse(xdebug_get_function_stack()), 4);

                foreach ($trace as $i => $frame) {
                    //  XDebug pre 2.1.1 doesn't currently set the call type key http://bugs.xdebug.org/view.php?id=695
                    if (!isset($frame['type'])) {
                        $trace[$i]['type'] = '??';
                    }

                    if ('dynamic' === $trace[$i]['type']) {
                        $trace[$i]['type'] = '->';
                    } elseif ('static' === $trace[$i]['type']) {
                        $trace[$i]['type'] = '::';
                    }

                    // XDebug also has a different name for the parameters array
                    if (isset($frame['params']) && !isset($frame['args'])) {
                        $trace[$i]['args'] = $frame['params'];
                        unset($trace[$i]['params']);
                    }
                }
            } else {
                $trace = array_slice(array_reverse($trace), 1);
            }
        }

        $this->setTrace($trace, $exception->getFile(), $exception->getLine());
    }

    public function setTrace($trace, $file, $line)
    {
        $this->trace = array();
        $this->trace[] = array(
            'namespace'   => '',
            'short_class' => '',
            'class'       => '',
            'type'        => '',
            'function'    => '',
            'file'        => $file,
            'line'        => $line,
            'args'        => array(),
        );
        foreach ($trace as $entry) {
            $class = '';
            $namespace = '';
            if (isset($entry['class'])) {
                $parts = explode('\\', $entry['class']);
                $class = array_pop($parts);
                $namespace = implode('\\', $parts);
            }

            $this->trace[] = array(
                'namespace'   => $namespace,
                'short_class' => $class,
                'class'       => isset($entry['class']) ? $entry['class'] : '',
                'type'        => isset($entry['type']) ? $entry['type'] : '',
                'function'    => isset($entry['function']) ? $entry['function'] : null,
                'file'        => isset($entry['file']) ? $entry['file'] : null,
                'line'        => isset($entry['line']) ? $entry['line'] : null,
                'args'        => isset($entry['args']) ? $this->flattenArgs($entry['args']) : array(),
            );
        }
    }

    private function flattenArgs($args, $level = 0)
    {
        $result = array();
        foreach ($args as $key => $value) {
            if (is_object($value)) {
                $result[$key] = array('object', get_class($value));
            } elseif (is_array($value)) {
                if ($level > 10) {
                    $result[$key] = array('array', '*DEEP NESTED ARRAY*');
                } else {
                    $result[$key] = array('array', $this->flattenArgs($value, $level + 1));
                }
            } elseif (null === $value) {
                $result[$key] = array('null', null);
            } elseif (is_bool($value)) {
                $result[$key] = array('boolean', $value);
            } elseif (is_resource($value)) {
                $result[$key] = array('resource', get_resource_type($value));
            } elseif ($value instanceof \__PHP_Incomplete_Class) {
                // Special case of object, is_object will return false
                $result[$key] = array('incomplete-object', $this->getClassNameFromIncomplete($value));
            } else {
                $result[$key] = array('string', (string) $value);
            }
        }

        return $result;
    }

    private function getClassNameFromIncomplete(\__PHP_Incomplete_Class $value)
    {
        $array = new \ArrayObject($value);

        return $array['__PHP_Incomplete_Class_Name'];
    }
}
