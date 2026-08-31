<?php

namespace Illuminate\View\Compilers\Concerns;

use Closure;
use Illuminate\Support\Stringable;

trait CompilesEchos
{
    /**
     * Custom rendering callbacks for stringable objects.
     *
     * @var array
     */
    protected $echoHandlers = [];

    /**
     * Add a handler to be executed before echoing a given class.
     *
     * @param  string|callable  $class
     * @param  callable|null  $handler
     * @return void
     */
    public function stringable($class, $handler = null)
    {
        if ($class instanceof Closure) {
            [$class, $handler] = [$this->firstClosureParameterType($class), $class];
        }

        $this->echoHandlers[$class] = $handler;
    }

    /**
     * Compile Blade echos into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    public function compileEchos($value)
    {
        foreach ($this->getEchoMethods() as $method) {
            $value = $this->$method($value);
        }

        return $value;
    }

    /**
     * Get the echo methods in the proper order for compilation.
     *
     * @return array
     */
    protected function getEchoMethods()
    {
        return [
            'compileRawEchos',
            'compileEscapedEchos',
            'compileRegularEchos',
        ];
    }

    /**
     * Compile the "raw" echo statements.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileRawEchos($value)
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->rawTags[0], $this->rawTags[1]);

        $callback = function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3].$matches[3];

            return $matches[1]
                ? substr($matches[0], 1)
                : "<?php echo {$this->wrapInEchoHandler($matches[2])}; ?>{$whitespace}";
        };

        return preg_replace_callback($pattern, $callback, $value);
    }

    /**
     * Compile the "regular" echo statements.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileRegularEchos($value)
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->contentTags[0], $this->contentTags[1]);

        $callback = function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3].$matches[3];

            $wrapped = sprintf($this->echoFormat, $this->wrapInEchoHandler($matches[2]));

            return $matches[1] ? substr($matches[0], 1) : "<?php echo {$wrapped}; ?>{$whitespace}";
        };

        return preg_replace_callback($pattern, $callback, $value);
    }

    /**
     * Compile the escaped echo statements.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileEscapedEchos($value)
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->escapedTags[0], $this->escapedTags[1]);

        $callback = function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3].$matches[3];

            return $matches[1]
                ? $matches[0]
                : "<?php echo e({$this->wrapInEchoHandler($matches[2])}); ?>{$whitespace}";
        };

        return preg_replace_callback($pattern, $callback, $value);
    }

    /**
     * Add an instance of the blade echo handler to the start of the compiled string.
     *
     * @param  string  $result
     * @return string
     */
    protected function addBladeCompilerVariable($result)
    {
        return "<?php \$__bladeCompiler = app('blade.compiler'); ?>".$result;
    }

    /**
     * Wrap the echoable value in an echo handler if applicable.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapInEchoHandler($value)
    {
        $value = (new Stringable($value))
            ->trim()
            ->when(str_ends_with($value, ';'), function ($str) {
                return $str->beforeLast(';');
            });

        return empty($this->echoHandlers) ? $value : '$__bladeCompiler->applyEchoHandler('.$value.')';
    }

    /**
     * Apply the echo handler for the value if it exists.
     *
     * @param  string  $value
     * @return string
     */
    public function applyEchoHandler($value)
    {
        if (is_object($value) && isset($this->echoHandlers[get_class($value)])) {
            return call_user_func($this->echoHandlers[get_class($value)], $value);
        }

        if (is_iterable($value) && isset($this->echoHandlers['iterable'])) {
            return call_user_func($this->echoHandlers['iterable'], $value);
        }

        return $value;
    }
}
