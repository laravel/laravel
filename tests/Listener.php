<?php

namespace Tests;

use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\TestListener;
use Illuminate\Contracts\Console\Kernel;
use PHPUnit\Framework\TestListenerDefaultImplementation;

class Listener implements TestListener
{
    use CreatesApplication, TestListenerDefaultImplementation;

    protected $configCached = false;

    public function startTestSuite(TestSuite $suite): void
    {
        if ($this->configCached) {
            return;
        }

        $this->createApplication()
            ->make(Kernel::class)
            ->call('config:cache');

        $this->configCached = true;
    }
}
