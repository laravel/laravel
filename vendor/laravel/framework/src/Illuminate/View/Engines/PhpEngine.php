<?php

namespace Illuminate\View\Engines;

use Illuminate\Contracts\View\Engine;
use Illuminate\Filesystem\Filesystem;
use Throwable;

class PhpEngine implements Engine
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new file engine instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path
     * @param  array  $data
     * @return string
     */
    public function get($path, array $data = [])
    {
        return $this->evaluatePath($path, $data);
    }

    /**
     * Get the evaluated contents of the view at the given path.
     *
     * @param  string  $path
     * @param  array  $data
     * @return string
     */
    protected function evaluatePath($path, $data)
    {
        $obLevel = ob_get_level();

        ob_start();

        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        try {
            $this->files->getRequire($path, $data);
        } catch (Throwable $e) {
            $this->handleViewException($e, $obLevel);
        }

        return ltrim(ob_get_clean());
    }

    /**
     * Handle a view exception.
     *
     * @param  \Throwable  $e
     * @param  int  $obLevel
     * @return void
     *
     * @throws \Throwable
     */
    protected function handleViewException(Throwable $e, $obLevel)
    {
        while (ob_get_level() > $obLevel) {
            ob_end_clean();
        }

        throw $e;
    }
}
