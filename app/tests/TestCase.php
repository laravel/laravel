<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

    /**
     * Creates the application.
     *
     * @return Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        $this->startWorkbench();

        return require __DIR__.'/../../start.php';
    }

    /**
     * Starts the workbench.
     *
     * @return void
     */
    public function startWorkbench()
    {
        if (is_dir($workbench = __DIR__.'/../../workbench'))
        {
            Illuminate\Workbench\Starter::start($workbench);
        }
    }

}
