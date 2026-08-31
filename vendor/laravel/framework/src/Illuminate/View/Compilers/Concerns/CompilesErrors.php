<?php

namespace Illuminate\View\Compilers\Concerns;

trait CompilesErrors
{
    /**
     * Compile the error statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileError($expression)
    {
        $expression = $this->stripParentheses($expression);

        return '<?php $__errorArgs = ['.$expression.'];
$__bag = $errors->getBag($__errorArgs[1] ?? \'default\');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>';
    }

    /**
     * Compile the enderror statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileEnderror($expression)
    {
        return '<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>';
    }
}
