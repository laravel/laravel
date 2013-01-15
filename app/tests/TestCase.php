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

        $basePath = __DIR__.'/../..';

        Illuminate\Workbench\Starter::start($basePath.'/workbench');

        return require $basePath.'/start.php';
    }

}
