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

class SigchildDisabledProcessTest extends AbstractProcessTest
{
    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. You must use setEnhanceSigchildCompatibility() to use this method.
     */
    public function testGetExitCode()
    {
        parent::testGetExitCode();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. You must use setEnhanceSigchildCompatibility() to use this method.
     */
    public function testGetExitCodeIsNullOnStart()
    {
        parent::testGetExitCodeIsNullOnStart();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. You must use setEnhanceSigchildCompatibility() to use this method.
     */
    public function testGetExitCodeIsNullOnWhenStartingAgain()
    {
        parent::testGetExitCodeIsNullOnWhenStartingAgain();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. You must use setEnhanceSigchildCompatibility() to use this method.
     */
    public function testExitCodeCommandFailed()
    {
        parent::testExitCodeCommandFailed();
    }

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
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. You must use setEnhanceSigchildCompatibility() to use this method.
     */
    public function testCheckTimeoutOnStartedProcess()
    {
        parent::testCheckTimeoutOnStartedProcess();
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

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. You must use setEnhanceSigchildCompatibility() to use this method.
     */
    public function testExitCodeText()
    {
        $process = $this->getProcess('qdfsmfkqsdfmqmsd');
        $process->run();

        $process->getExitCodeText();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. You must use setEnhanceSigchildCompatibility() to use this method.
     */
    public function testExitCodeTextIsNullWhenExitCodeIsNull()
    {
        parent::testExitCodeTextIsNullWhenExitCodeIsNull();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. You must use setEnhanceSigchildCompatibility() to use this method.
     */
    public function testIsSuccessful()
    {
        parent::testIsSuccessful();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. You must use setEnhanceSigchildCompatibility() to use this method.
     */
    public function testIsSuccessfulOnlyAfterTerminated()
    {
        parent::testIsSuccessfulOnlyAfterTerminated();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. You must use setEnhanceSigchildCompatibility() to use this method.
     */
    public function testIsNotSuccessful()
    {
        parent::testIsNotSuccessful();
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage This PHP has been compiled with --enable-sigchild. You must use setEnhanceSigchildCompatibility() to use this method.
     */
    public function testTTYCommandExitCode()
    {
        parent::testTTYCommandExitCode();
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

    public function testStopWithTimeoutIsActuallyWorking()
    {
        $this->markTestSkipped('Stopping with signal is not supported in sigchild environment');
    }

    public function testProcessThrowsExceptionWhenExternallySignaled()
    {
        $this->markTestSkipped('Retrieving Pid is not supported in sigchild environment');
    }

    public function testExitCodeIsAvailableAfterSignal()
    {
        $this->markTestSkipped('Signal is not supported in sigchild environment');
    }

    public function testRunProcessWithTimeout()
    {
        $this->markTestSkipped('Signal (required for timeout) is not supported in sigchild environment');
    }

    /**
     * {@inheritdoc}
     */
    protected function getProcess($commandline, $cwd = null, array $env = null, $stdin = null, $timeout = 60, array $options = array())
    {
        $process = new ProcessInSigchildEnvironment($commandline, $cwd, $env, $stdin, $timeout, $options);
        $process->setEnhanceSigchildCompatibility(false);

        return $process;
    }
}
