<?php

namespace Illuminate\View\Compilers\Concerns;

trait CompilesTranslations
{
    /**
     * Compile the lang statements into valid PHP.
     *
     * @param  string|null  $expression
     * @return string
     */
    protected function compileLang($expression)
    {
        if (is_null($expression)) {
            return '<?php $__env->startTranslation(); ?>';
        } elseif ($expression[1] === '[') {
            return "<?php \$__env->startTranslation{$expression}; ?>";
        }

        return "<?php echo app('translator')->get{$expression}; ?>";
    }

    /**
     * Compile the end-lang statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndlang()
    {
        return '<?php echo $__env->renderTranslation(); ?>';
    }

    /**
     * Compile the choice statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileChoice($expression)
    {
        return "<?php echo app('translator')->choice{$expression}; ?>";
    }
}
