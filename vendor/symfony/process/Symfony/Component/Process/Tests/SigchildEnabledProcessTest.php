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

class SigchildEnabledProcessTest extends AbstractProcessTest
{
    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. Term signal can not be retrieved.
     */
    public function testProcessIsSignaledIfStopped()
    {
        parent::testProcessIsSignaledIfStopped();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. Term signal can not be retrieved.
     */
    public function testProcessWithTermSignal()
    {
        parent::testProcessWithTermSignal();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. Term signal can not be retrieved.
     */
    public function testProcessIsNotSignaled()
    {
        parent::testProcessIsNotSignaled();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. Term signal can not be retrieved.
     */
    public function testProcessWithoutTermSignal()
    {
        parent::testProcessWithoutTermSignal();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. The process identifier can not be retrieved.
     */
    public function testGetPid()
    {
        parent::testGetPid();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. The process identifier can not be retrieved.
     */
    public function testGetPidIsNullBeforeStart()
    {
        parent::testGetPidIsNullBeforeStart();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. The process identifier can not be retrieved.
     */
    public function testGetPidIsNullAfterRun()
    {
        parent::testGetPidIsNullAfterRun();
    }

    public function testExitCodeText()
    {
        $process = $this->getProcess('qdfsmfkqsdfmqmsd');
        $process->run();

        $this->assertInternalType('string', $process->getExitCodeText());
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. The process can not be signaled.
     */
    public function testSignal()
    {
        parent::testSignal();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. Term signal can not be retrieved.
     */
    public function testProcessWithoutTermSignalIsNotSignaled()
    {
        parent::testProcessWithoutTermSignalIsNotSignaled();
    }

    public function testProcessThrowsExceptionWhenExternallySignaled()
    {
        $this->markTestSkipped('Retrieving Pid is not supported in sigchild environment');
    }

    public function testExitCodeIsAvailableAfterSignal()
    {
        $this->markTestSkipped('Signal is not supported in sigchild environment');
    }

    public function testStartAfterATimeout()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Restarting a timed-out process on Windows is not supported in sigchild environment');
        }
        parent::testStartAfterATimeout();
    }

    public function testStopWithTimeoutIsActuallyWorking()
    {
        $this->markTestSkipped('Stopping with signal is not supported in sigchild environment');
    }

    public function testRunProcessWithTimeout()
    {
        $this->markTestSkipped('Signal (required for timeout) is not supported in sigchild environment');
    }

    public function testCheckTimeoutOnStartedProcess()
    {
        $this->markTestSkipped('Signal (required for timeout) is not supported in sigchild environment');
    }

    /**
     * {@inheritdoc}
     */
    protected function getProcess($commandline, $cwd = null, array $env = null, $stdin = null, $timeout = 60, array $options = array())
    {
        $process = new ProcessInSigchildEnvironment($commandline, $cwd, $env, $stdin, $timeout, $options);
        $process->setEnhanceSigchildCompatibility(true);

        return $process;
    }
}
