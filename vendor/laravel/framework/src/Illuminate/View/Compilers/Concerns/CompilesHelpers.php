<?php

namespace Illuminate\View\Compilers\Concerns;

use Illuminate\Foundation\Vite;

trait CompilesHelpers
{
    /**
     * Compile the CSRF statements into valid PHP.
     *
     * @return string
     */
    protected function compileCsrf()
    {
        return '<?php echo csrf_field(); ?>';
    }

    /**
     * Compile the "dd" statements into valid PHP.
     *
     * @param  string  $arguments
     * @return string
     */
    protected function compileDd($arguments)
    {
        return "<?php dd{$arguments}; ?>";
    }

    /**
     * Compile the "dump" statements into valid PHP.
     *
     * @param  string  $arguments
     * @return string
     */
    protected function compileDump($arguments)
    {
        return "<?php dump{$arguments}; ?>";
    }

    /**
     * Compile the method statements into valid PHP.
     *
     * @param  string  $method
     * @return string
     */
    protected function compileMethod($method)
    {
        return "<?php echo method_field{$method}; ?>";
    }

    /**
     * Compile the "vite" statements into valid PHP.
     *
     * @param  string|null  $arguments
     * @return string
     */
    protected function compileVite($arguments)
    {
        $arguments ??= '()';

        $class = Vite::class;

        return "<?php echo app('$class'){$arguments}; ?>";
    }

    /**
     * Compile the "viteReactRefresh" statements into valid PHP.
     *
     * @return string
     */
    protected function compileViteReactRefresh()
    {
        $class = Vite::class;

        return "<?php echo app('$class')->reactRefresh(); ?>";
    }
}
