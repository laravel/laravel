<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Process\Tests;

use Symfony\Component\Process\Process;

class SimpleProcessTest extends AbstractProcessTest
{
    private $enabledSigchild = false;

    public function setUp()
    {
        ob_start();
        phpinfo(INFO_GENERAL);

        $this->enabledSigchild = false !== strpos(ob_get_clean(), '--enable-sigchild');
    }

    public function testGetExitCode()
    {
        $this->skipIfPHPSigchild(); // This test use exitcode that is not available in this case
        parent::testGetExitCode();
    }

    public function testExitCodeCommandFailed()
    {
        $this->skipIfPHPSigchild(); // This test use exitcode that is not available in this case
        parent::testExitCodeCommandFailed();
    }

    public function testProcessIsSignaledIfStopped()
    {
        $this->expectExceptionIfPHPSigchild('Symfony\Component\Process\Exception\RuntimeException', 'This PHP has been compiled with --enable-sigchild. Term signal can not be retrieved');
        parent::testProcessIsSignaledIfStopped();
    }

    public function testProcessWithTermSignal()
    {
        $this->expectExceptionIfPHPSigchild('Symfony\Component\Process\Exception\RuntimeException', 'This PHP has been compiled with --enable-sigchild. Term signal can not be retrieved');
        parent::testProcessWithTermSignal();
    }

    public function testProcessIsNotSignaled()
    {
        $this->expectExceptionIfPHPSigchild('Symfony\Component\Process\Exception\RuntimeException', 'This PHP has been compiled with --enable-sigchild. Term signal can not be retrieved');
        parent::testProcessIsNotSignaled();
    }

    public function testProcessWithoutTermSignal()
    {
        $this->expectExceptionIfPHPSigchild('Symfony\Component\Process\Exception\RuntimeException', 'This PHP has been compiled with --enable-sigchild. Term signal can not be retrieved');
        parent::testProcessWithoutTermSignal();
    }

    public function testExitCodeText()
    {
        $this->skipIfPHPSigchild(); // This test use exitcode that is not available in this case
        parent::testExitCodeText();
    }

    public function testIsSuccessful()
    {
        $this->skipIfPHPSigchild(); // This test use PID that is not available in this case
        parent::testIsSuccessful();
    }

    public function testIsNotSuccessful()
    {
        $this->skipIfPHPSigchild(); // This test use PID that is not available in this case
        parent::testIsNotSuccessful();
    }

    public function testGetPid()
    {
        $this->skipIfPHPSigchild(); // This test use PID that is not available in this case
        parent::testGetPid();
    }

    public function testGetPidIsNullBeforeStart()
    {
        $this->skipIfPHPSigchild(); // This test use PID that is not available in this case
        parent::testGetPidIsNullBeforeStart();
    }

    public function testGetPidIsNullAfterRun()
    {
        $this->skipIfPHPSigchild(); // This test use PID that is not available in this case
        parent::testGetPidIsNullAfterRun();
    }

    public function testSignal()
    {
        $this->expectExceptionIfPHPSigchild('Symfony\Component\Process\Exception\RuntimeException', 'This PHP has been compiled with --enable-sigchild. The process can not be signaled.');
        parent::testSignal();
    }

    public function testProcessWithoutTermSignalIsNotSignaled()
    {
        $this->expectExceptionIfPHPSigchild('Symfony\Component\Process\Exception\RuntimeException', 'This PHP has been compiled with --enable-sigchild. Term signal can not be retrieved');
        parent::testProcessWithoutTermSignalIsNotSignaled();
    }

    public function testProcessThrowsExceptionWhenExternallySignaled()
    {
        $this->skipIfPHPSigchild(); // This test use PID that is not available in this case
        parent::testProcessThrowsExceptionWhenExternallySignaled();
    }

    public function testExitCodeIsAvailableAfterSignal()
    {
        $this->expectExceptionIfPHPSigchild('Symfony\Component\Process\Exception\RuntimeException', 'This PHP has been compiled with --enable-sigchild. The process can not be signaled.');
        parent::testExitCodeIsAvailableAfterSignal();
    }

    public function testSignalProcessNotRunning()
    {
        $this->setExpectedException('Symfony\Component\Process\Exception\LogicException', 'Can not send signal on a non running process.');
        parent::testSignalProcessNotRunning();
    }

    public function testSignalWithWrongIntSignal()
    {
        if ($this->enabledSigchild) {
            $this->expectExceptionIfPHPSigchild('Symfony\Component\Process\Exception\RuntimeException', 'This PHP has been compiled with --enable-sigchild. The process can not be signaled.');
        } else {
            $this->setExpectedException('Symfony\Component\Process\Exception\RuntimeException', 'Error while sending signal `-4`.');
        }
        parent::testSignalWithWrongIntSignal();
    }

    public function testSignalWithWrongNonIntSignal()
    {
        if ($this->enabledSigchild) {
            $this->expectExceptionIfPHPSigchild('Symfony\Component\Process\Exception\RuntimeException', 'This PHP has been compiled with --enable-sigchild. The process can not be signaled.');
        } else {
            $this->setExpectedException('Symfony\Component\Process\Exception\RuntimeException', 'Error while sending signal `Céphalopodes`.');
        }
        parent::testSignalWithWrongNonIntSignal();
    }

    public function testStopTerminatesProcessCleanly()
    {
        try {
            $process = $this->getProcess('php -r "echo \'foo\'; sleep(1); echo \'bar\';"');
            $process->run(function () use ($process) {
                $process->stop();
            });
        } catch (RuntimeException $e) {
            $this->fail('A call to stop() is not expected to cause wait() to throw a RuntimeException');
        }
    }

    public function testKillSignalTerminatesProcessCleanly()
    {
        $this->expectExceptionIfPHPSigchild('Symfony\Component\Process\Exception\RuntimeException', 'This PHP has been compiled with --enable-sigchild. The process can not be signaled.');

        try {
            $process = $this->getProcess('php -r "echo \'foo\'; sleep(1); echo \'bar\';"');
            $process->run(function () use ($process) {
                if ($process->isRunning()) {
                    $process->signal(defined('SIGKILL') ? SIGKILL : 9);
                }
            });
        } catch (RuntimeException $e) {
            $this->fail('A call to signal() is not expected to cause wait() to throw a RuntimeException');
        }
    }

    public function testTermSignalTerminatesProcessCleanly()
    {
        $this->expectExceptionIfPHPSigchild('Symfony\Component\Process\Exception\RuntimeException', 'This PHP has been compiled with --enable-sigchild. The process can not be signaled.');

        try {
            $process = $this->getProcess('php -r "echo \'foo\'; sleep(1); echo \'bar\';"');
            $process->run(function () use ($process) {
                if ($process->isRunning()) {
                    $process->signal(defined('SIGTERM') ? SIGTERM : 15);
                }
            });
        } catch (RuntimeException $e) {
            $this->fail('A call to signal() is not expected to cause wait() to throw a RuntimeException');
        }
    }

    public function testStopWithTimeoutIsActuallyWorking()
    {
        $this->skipIfPHPSigchild();

        parent::testStopWithTimeoutIsActuallyWorking();
    }

    /**
     * {@inheritdoc}
     */
    protected function getProcess($commandline, $cwd = null, array $env = null, $stdin = null, $timeout = 60, array $options = array())
    {
        return new Process($commandline, $cwd, $env, $stdin, $timeout, $options);
    }

    private function skipIfPHPSigchild()
    {
        if ($this->enabledSigchild) {
            $this->markTestSkipped('Your PHP has been compiled with --enable-sigchild, this test can not be executed');
        }
    }

    private function expectExceptionIfPHPSigchild($classname, $message)
    {
        if ($this->enabledSigchild) {
            $this->setExpectedException($classname, $message);
        }
    }
}
