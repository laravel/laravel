<?php

namespace Illuminate\View\Compilers\Concerns;

trait CompilesIncludes
{
    /**
     * Compile the each statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileEach($expression)
    {
        return "<?php echo \$__env->renderEach{$expression}; ?>";
    }

    /**
     * Compile the include statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileInclude($expression)
    {
        $expression = $this->stripParentheses($expression);

        return "<?php echo \$__env->make({$expression}, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>";
    }

    /**
     * Compile the include-if statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileIncludeIf($expression)
    {
        $expression = $this->stripParentheses($expression);

        return "<?php if (\$__env->exists({$expression})) echo \$__env->make({$expression}, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>";
    }

    /**
     * Compile the include-when statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileIncludeWhen($expression)
    {
        $expression = $this->stripParentheses($expression);

        return "<?php echo \$__env->renderWhen($expression, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>";
    }

    /**
     * Compile the include-unless statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileIncludeUnless($expression)
    {
        $expression = $this->stripParentheses($expression);

        return "<?php echo \$__env->renderUnless($expression, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>";
    }

    /**
     * Compile the include-first statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileIncludeFirst($expression)
    {
        $expression = $this->stripParentheses($expression);

        return "<?php echo \$__env->first({$expression}, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>";
    }

    /**
     * Compile the include-isolated statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileIncludeIsolated($expression)
    {
        $expression = $this->stripParentheses($expression);

        return "<?php echo \$__env->make({$expression})->render(); ?>";
    }
}
