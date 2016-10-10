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

use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\ProcessPipes;

/**
 * @author Robert Schönthal <seroscho@googlemail.com>
 */
abstract class AbstractProcessTest extends \PHPUnit_Framework_TestCase
{
    public function testThatProcessDoesNotThrowWarningDuringRun()
    {
        @trigger_error('Test Error', E_USER_NOTICE);
        $process = $this->getProcess("php -r 'sleep(3)'");
        $process->run();
        $actualError = error_get_last();
        $this->assertEquals('Test Error', $actualError['message']);
        $this->assertEquals(E_USER_NOTICE, $actualError['type']);
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\InvalidArgumentException
     */
    public function testNegativeTimeoutFromConstructor()
    {
        $this->getProcess('', null, null, null, -1);
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\InvalidArgumentException
     */
    public function testNegativeTimeoutFromSetter()
    {
        $p = $this->getProcess('');
        $p->setTimeout(-1);
    }

    public function testFloatAndNullTimeout()
    {
        $p = $this->getProcess('');

        $p->setTimeout(10);
        $this->assertSame(10.0, $p->getTimeout());

        $p->setTimeout(null);
        $this->assertNull($p->getTimeout());

        $p->setTimeout(0.0);
        $this->assertNull($p->getTimeout());
    }

    public function testStopWithTimeoutIsActuallyWorking()
    {
        $this->verifyPosixIsEnabled();

        // exec is mandatory here since we send a signal to the process
        // see https://github.com/symfony/symfony/issues/5030 about prepending
        // command with exec
        $p = $this->getProcess('exec php '.__DIR__.'/NonStopableProcess.php 3');
        $p->start();
        usleep(100000);
        $start = microtime(true);
        $p->stop(1.1, SIGKILL);
        while ($p->isRunning()) {
            usleep(1000);
        }

        $this->assertLessThan(4, microtime(true) - $start);
    }

    public function testAllOutputIsActuallyReadOnTermination()
    {
        // this code will result in a maximum of 2 reads of 8192 bytes by calling
        // start() and isRunning().  by the time getOutput() is called the process
        // has terminated so the internal pipes array is already empty. normally
        // the call to start() will not read any data as the process will not have
        // generated output, but this is non-deterministic so we must count it as
        // a possibility.  therefore we need 2 * ProcessPipes::CHUNK_SIZE plus
        // another byte which will never be read.
        $expectedOutputSize = ProcessPipes::CHUNK_SIZE * 2 + 2;

        $code = sprintf('echo str_repeat(\'*\', %d);', $expectedOutputSize);
        $p = $this->getProcess(sprintf('php -r %s', escapeshellarg($code)));

        $p->start();
        // Let's wait enough time for process to finish...
        // Here we don't call Process::run or Process::wait to avoid any read of pipes
        usleep(500000);

        if ($p->isRunning()) {
            $this->markTestSkipped('Process execution did not complete in the required time frame');
        }

        $o = $p->getOutput();

        $this->assertEquals($expectedOutputSize, strlen($o));
    }

    public function testCallbacksAreExecutedWithStart()
    {
        $data = '';

        $process = $this->getProcess('echo foo && php -r "sleep(1);" && echo foo');
        $process->start(function ($type, $buffer) use (&$data) {
            $data .= $buffer;
        });

        while ($process->isRunning()) {
            usleep(10000);
        }

        $this->assertEquals(2, preg_match_all('/foo/', $data, $matches));
    }

    /**
     * tests results from sub processes
     *
     * @dataProvider responsesCodeProvider
     */
    public function testProcessResponses($expected, $getter, $code)
    {
        $p = $this->getProcess(sprintf('php -r %s', escapeshellarg($code)));
        $p->run();

        $this->assertSame($expected, $p->$getter());
    }

    /**
     * tests results from sub processes
     *
     * @dataProvider pipesCodeProvider
     */
    public function testProcessPipes($code, $size)
    {
        $expected = str_repeat(str_repeat('*', 1024), $size).'!';
        $expectedLength = (1024 * $size) + 1;

        $p = $this->getProcess(sprintf('php -r %s', escapeshellarg($code)));
        $p->setStdin($expected);
        $p->run();

        $this->assertEquals($expectedLength, strlen($p->getOutput()));
        $this->assertEquals($expectedLength, strlen($p->getErrorOutput()));
    }

    public function testSetStdinWhileRunningThrowsAnException()
    {
        $process = $this->getProcess('php -r "usleep(500000);"');
        $process->start();
        try {
            $process->setStdin('foobar');
            $process->stop();
            $this->fail('A LogicException should have been raised.');
        } catch (LogicException $e) {
            $this->assertEquals('STDIN can not be set while the process is running.', $e->getMessage());
        }
        $process->stop();
    }

    /**
     * @dataProvider provideInvalidStdinValues
     * @expectedException \Symfony\Component\Process\Exception\InvalidArgumentException
     * @expectedExceptionMessage Symfony\Component\Process\Process::setStdin only accepts strings.
     */
    public function testInvalidStdin($value)
    {
        $process = $this->getProcess('php -v');
        $process->setStdin($value);
    }

    public function provideInvalidStdinValues()
    {
        return array(
            array(array()),
            array(new NonStringifiable()),
            array(fopen('php://temporary', 'w')),
        );
    }

    /**
     * @dataProvider provideStdinValues
     */
    public function testValidStdin($expected, $value)
    {
        $process = $this->getProcess('php -v');
        $process->setStdin($value);
        $this->assertSame($expected, $process->getStdin());
    }

    public function provideStdinValues()
    {
        return array(
            array(null, null),
            array('24.5', 24.5),
            array('input data', 'input data'),
            // to maintain BC, supposed to be removed in 3.0
            array('stringifiable', new Stringifiable()),
        );
    }

    public function chainedCommandsOutputProvider()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            return array(
                array("2 \r\n2\r\n", '&&', '2'),
            );
        }

        return array(
            array("1\n1\n", ';', '1'),
            array("2\n2\n", '&&', '2'),
        );
    }

    /**
     *
     * @dataProvider chainedCommandsOutputProvider
     */
    public function testChainedCommandsOutput($expected, $operator, $input)
    {
        $process = $this->getProcess(sprintf('echo %s %s echo %s', $input, $operator, $input));
        $process->run();
        $this->assertEquals($expected, $process->getOutput());
    }

    public function testCallbackIsExecutedForOutput()
    {
        $p = $this->getProcess(sprintf('php -r %s', escapeshellarg('echo \'foo\';')));

        $called = false;
        $p->run(function ($type, $buffer) use (&$called) {
            $called = $buffer === 'foo';
        });

        $this->assertTrue($called, 'The callback should be executed with the output');
    }

    public function testGetErrorOutput()
    {
        $p = $this->getProcess(sprintf('php -r %s', escapeshellarg('$n = 0; while ($n < 3) { file_put_contents(\'php://stderr\', \'ERROR\'); $n++; }')));

        $p->run();
        $this->assertEquals(3, preg_match_all('/ERROR/', $p->getErrorOutput(), $matches));
    }

    public function testGetIncrementalErrorOutput()
    {
        // use a lock file to toggle between writing ("W") and reading ("R") the
        // error stream
        $lock = tempnam(sys_get_temp_dir(), get_class($this).'Lock');
        file_put_contents($lock, 'W');

        $p = $this->getProcess(sprintf('php -r %s', escapeshellarg('$n = 0; while ($n < 3) { if (\'W\' === file_get_contents('.var_export($lock, true).')) { file_put_contents(\'php://stderr\', \'ERROR\'); $n++; file_put_contents('.var_export($lock, true).', \'R\'); } usleep(100); }')));

        $p->start();
        while ($p->isRunning()) {
            if ('R' === file_get_contents($lock)) {
                $this->assertLessThanOrEqual(1, preg_match_all('/ERROR/', $p->getIncrementalErrorOutput(), $matches));
                file_put_contents($lock, 'W');
            }
            usleep(100);
        }

        unlink($lock);
    }

    public function testFlushErrorOutput()
    {
        $p = $this->getProcess(sprintf('php -r %s', escapeshellarg('$n = 0; while ($n < 3) { file_put_contents(\'php://stderr\', \'ERROR\'); $n++; }')));

        $p->run();
        $p->clearErrorOutput();
        $this->assertEmpty($p->getErrorOutput());
    }

    public function testGetOutput()
    {
        $p = $this->getProcess(sprintf('php -r %s', escapeshellarg('$n = 0; while ($n < 3) { echo \' foo \'; $n++; }')));

        $p->run();
        $this->assertEquals(3, preg_match_all('/foo/', $p->getOutput(), $matches));
    }

    public function testGetIncrementalOutput()
    {
        // use a lock file to toggle between writing ("W") and reading ("R") the
        // output stream
        $lock = tempnam(sys_get_temp_dir(), get_class($this).'Lock');
        file_put_contents($lock, 'W');

        $p = $this->getProcess(sprintf('php -r %s', escapeshellarg('$n = 0; while ($n < 3) { if (\'W\' === file_get_contents('.var_export($lock, true).')) { echo \' foo \'; $n++; file_put_contents('.var_export($lock, true).', \'R\'); } usleep(100); }')));

        $p->start();
        while ($p->isRunning()) {
            if ('R' === file_get_contents($lock)) {
                $this->assertLessThanOrEqual(1, preg_match_all('/foo/', $p->getIncrementalOutput(), $matches));
                file_put_contents($lock, 'W');
            }
            usleep(100);
        }

        unlink($lock);
    }

    public function testFlushOutput()
    {
        $p = $this->getProcess(sprintf('php -r %s', escapeshellarg('$n=0;while ($n<3) {echo \' foo \';$n++;}')));

        $p->run();
        $p->clearOutput();
        $this->assertEmpty($p->getOutput());
    }

    public function testZeroAsOutput()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            // see http://stackoverflow.com/questions/7105433/windows-batch-echo-without-new-line
            $p = $this->getProcess('echo | set /p dummyName=0');
        } else {
            $p = $this->getProcess('printf 0');
        }

        $p->run();
        $this->assertSame('0', $p->getOutput());
    }

    public function testExitCodeCommandFailed()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Windows does not support POSIX exit code');
        }

        // such command run in bash return an exitcode 127
        $process = $this->getProcess('nonexistingcommandIhopeneversomeonewouldnameacommandlikethis');
        $process->run();

        $this->assertGreaterThan(0, $process->getExitCode());
    }

    public function testTTYCommand()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Windows does have /dev/tty support');
        }

        $process = $this->getProcess('echo "foo" >> /dev/null && php -r "usleep(100000);"');
        $process->setTty(true);
        $process->start();
        $this->assertTrue($process->isRunning());
        $process->wait();

        $this->assertSame(Process::STATUS_TERMINATED, $process->getStatus());
    }

    public function testTTYCommandExitCode()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Windows does have /dev/tty support');
        }

        $process = $this->getProcess('echo "foo" >> /dev/null');
        $process->setTty(true);
        $process->run();

        $this->assertTrue($process->isSuccessful());
    }

    public function testTTYInWindowsEnvironment()
    {
        if (!defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('This test is for Windows platform only');
        }

        $process = $this->getProcess('echo "foo" >> /dev/null');
        $process->setTty(false);
        $this->setExpectedException('Symfony\Component\Process\Exception\RuntimeException', 'TTY mode is not supported on Windows platform.');
        $process->setTty(true);
    }

    public function testExitCodeTextIsNullWhenExitCodeIsNull()
    {
        $process = $this->getProcess('');
        $this->assertNull($process->getExitCodeText());
    }

    public function testExitCodeText()
    {
        $process = $this->getProcess('');
        $r = new \ReflectionObject($process);
        $p = $r->getProperty('exitcode');
        $p->setAccessible(true);

        $p->setValue($process, 2);
        $this->assertEquals('Misuse of shell builtins', $process->getExitCodeText());
    }

    public function testStartIsNonBlocking()
    {
        $process = $this->getProcess('php -r "usleep(500000);"');
        $start = microtime(true);
        $process->start();
        $end = microtime(true);
        $this->assertLessThan(1, $end - $start);
        $process->wait();
    }

    public function testUpdateStatus()
    {
        $process = $this->getProcess('php -h');
        $process->run();
        $this->assertTrue(strlen($process->getOutput()) > 0);
    }

    public function testGetExitCodeIsNullOnStart()
    {
        $process = $this->getProcess('php -r "usleep(200000);"');
        $this->assertNull($process->getExitCode());
        $process->start();
        $this->assertNull($process->getExitCode());
        $process->wait();
        $this->assertEquals(0, $process->getExitCode());
    }

    public function testGetExitCodeIsNullOnWhenStartingAgain()
    {
        $process = $this->getProcess('php -r "usleep(200000);"');
        $process->run();
        $this->assertEquals(0, $process->getExitCode());
        $process->start();
        $this->assertNull($process->getExitCode());
        $process->wait();
        $this->assertEquals(0, $process->getExitCode());
    }

    public function testGetExitCode()
    {
        $process = $this->getProcess('php -m');
        $process->run();
        $this->assertSame(0, $process->getExitCode());
    }

    public function testStatus()
    {
        $process = $this->getProcess('php -r "usleep(500000);"');
        $this->assertFalse($process->isRunning());
        $this->assertFalse($process->isStarted());
        $this->assertFalse($process->isTerminated());
        $this->assertSame(Process::STATUS_READY, $process->getStatus());
        $process->start();
        $this->assertTrue($process->isRunning());
        $this->assertTrue($process->isStarted());
        $this->assertFalse($process->isTerminated());
        $this->assertSame(Process::STATUS_STARTED, $process->getStatus());
        $process->wait();
        $this->assertFalse($process->isRunning());
        $this->assertTrue($process->isStarted());
        $this->assertTrue($process->isTerminated());
        $this->assertSame(Process::STATUS_TERMINATED, $process->getStatus());
    }

    public function testStop()
    {
        $process = $this->getProcess('php -r "sleep(4);"');
        $process->start();
        $this->assertTrue($process->isRunning());
        $process->stop();
        $this->assertFalse($process->isRunning());
    }

    public function testIsSuccessful()
    {
        $process = $this->getProcess('php -m');
        $process->run();
        $this->assertTrue($process->isSuccessful());
    }

    public function testIsSuccessfulOnlyAfterTerminated()
    {
        $process = $this->getProcess('php -r "sleep(1);"');
        $process->start();
        while ($process->isRunning()) {
            $this->assertFalse($process->isSuccessful());
            usleep(300000);
        }

        $this->assertTrue($process->isSuccessful());
    }

    public function testIsNotSuccessful()
    {
        $process = $this->getProcess('php -r "usleep(500000);throw new \Exception(\'BOUM\');"');
        $process->start();
        $this->assertTrue($process->isRunning());
        $process->wait();
        $this->assertFalse($process->isSuccessful());
    }

    public function testProcessIsNotSignaled()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Windows does not support POSIX signals');
        }

        $process = $this->getProcess('php -m');
        $process->run();
        $this->assertFalse($process->hasBeenSignaled());
    }

    public function testProcessWithoutTermSignalIsNotSignaled()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Windows does not support POSIX signals');
        }

        $process = $this->getProcess('php -m');
        $process->run();
        $this->assertFalse($process->hasBeenSignaled());
    }

    public function testProcessWithoutTermSignal()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Windows does not support POSIX signals');
        }

        $process = $this->getProcess('php -m');
        $process->run();
        $this->assertEquals(0, $process->getTermSignal());
    }

    public function testProcessIsSignaledIfStopped()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Windows does not support POSIX signals');
        }

        $process = $this->getProcess('php -r "sleep(4);"');
        $process->start();
        $process->stop();
        $this->assertTrue($process->hasBeenSignaled());
    }

    public function testProcessWithTermSignal()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Windows does not support POSIX signals');
        }

        // SIGTERM is only defined if pcntl extension is present
        $termSignal = defined('SIGTERM') ? SIGTERM : 15;

        $process = $this->getProcess('php -r "sleep(4);"');
        $process->start();
        $process->stop();

        $this->assertEquals($termSignal, $process->getTermSignal());
    }

    public function testProcessThrowsExceptionWhenExternallySignaled()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Windows does not support POSIX signals');
        }

        if (!function_exists('posix_kill')) {
            $this->markTestSkipped('posix_kill is required for this test');
        }

        $termSignal = defined('SIGKILL') ? SIGKILL : 9;

        $process = $this->getProcess('exec php -r "while (true) {}"');
        $process->start();
        posix_kill($process->getPid(), $termSignal);

        $this->setExpectedException('Symfony\Component\Process\Exception\RuntimeException', 'The process has been signaled with signal "9".');
        $process->wait();
    }

    public function testRestart()
    {
        $process1 = $this->getProcess('php -r "echo getmypid();"');
        $process1->run();
        $process2 = $process1->restart();

        $process2->wait(); // wait for output

        // Ensure that both processed finished and the output is numeric
        $this->assertFalse($process1->isRunning());
        $this->assertFalse($process2->isRunning());
        $this->assertTrue(is_numeric($process1->getOutput()));
        $this->assertTrue(is_numeric($process2->getOutput()));

        // Ensure that restart returned a new process by check that the output is different
        $this->assertNotEquals($process1->getOutput(), $process2->getOutput());
    }

    public function testPhpDeadlock()
    {
        $this->markTestSkipped('Can cause PHP to hang');

        // Sleep doesn't work as it will allow the process to handle signals and close
        // file handles from the other end.
        $process = $this->getProcess('php -r "while (true) {}"');
        $process->start();

        // PHP will deadlock when it tries to cleanup $process
    }

    public function testRunProcessWithTimeout()
    {
        $timeout = 0.5;
        $process = $this->getProcess('php -r "usleep(600000);"');
        $process->setTimeout($timeout);
        $start = microtime(true);
        try {
            $process->run();
            $this->fail('A RuntimeException should have been raised');
        } catch (RuntimeException $e) {
        }
        $duration = microtime(true) - $start;

        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            // Windows is a bit slower as it read file handles, then allow twice the precision
            $maxDuration = $timeout + 2 * Process::TIMEOUT_PRECISION;
        } else {
            $maxDuration = $timeout + Process::TIMEOUT_PRECISION;
        }

        $this->assertLessThan($maxDuration, $duration);
    }

    public function testCheckTimeoutOnNonStartedProcess()
    {
        $process = $this->getProcess('php -r "sleep(3);"');
        $process->checkTimeout();
    }

    public function testCheckTimeoutOnTerminatedProcess()
    {
        $process = $this->getProcess('php -v');
        $process->run();
        $process->checkTimeout();
    }

    public function testCheckTimeoutOnStartedProcess()
    {
        $timeout = 0.5;
        $precision = 100000;
        $process = $this->getProcess('php -r "sleep(3);"');
        $process->setTimeout($timeout);
        $start = microtime(true);

        $process->start();

        try {
            while ($process->isRunning()) {
                $process->checkTimeout();
                usleep($precision);
            }
            $this->fail('A RuntimeException should have been raised');
        } catch (RuntimeException $e) {
        }
        $duration = microtime(true) - $start;

        $this->assertLessThan($timeout + $precision, $duration);
        $this->assertFalse($process->isSuccessful());
    }

    /**
     * @group idle-timeout
     */
    public function testIdleTimeout()
    {
        $process = $this->getProcess('sleep 3');
        $process->setTimeout(10);
        $process->setIdleTimeout(1);

        try {
            $process->run();

            $this->fail('A timeout exception was expected.');
        } catch (ProcessTimedOutException $ex) {
            $this->assertTrue($ex->isIdleTimeout());
            $this->assertFalse($ex->isGeneralTimeout());
            $this->assertEquals(1.0, $ex->getExceededTimeout());
        }
    }

    /**
     * @group idle-timeout
     */
    public function testIdleTimeoutNotExceededWhenOutputIsSent()
    {
        $process = $this->getProcess('echo "foo" && sleep 1 && echo "foo" && sleep 1 && echo "foo" && sleep 1 && echo "foo" && sleep 5');
        $process->setTimeout(5);
        $process->setIdleTimeout(3);

        try {
            $process->run();
            $this->fail('A timeout exception was expected.');
        } catch (ProcessTimedOutException $ex) {
            $this->assertTrue($ex->isGeneralTimeout());
            $this->assertFalse($ex->isIdleTimeout());
            $this->assertEquals(5.0, $ex->getExceededTimeout());
        }
    }

    public function testStartAfterATimeout()
    {
        $process = $this->getProcess('php -r "$n = 1000; while ($n--) {echo \'\'; usleep(1000); }"');
        $process->setTimeout(0.1);
        try {
            $process->run();
            $this->fail('An exception should have been raised.');
        } catch (\Exception $e) {
        }
        $process->start();
        usleep(10000);
        $process->stop();
    }

    public function testGetPid()
    {
        $process = $this->getProcess('php -r "usleep(500000);"');
        $process->start();
        $this->assertGreaterThan(0, $process->getPid());
        $process->wait();
    }

    public function testGetPidIsNullBeforeStart()
    {
        $process = $this->getProcess('php -r "sleep(1);"');
        $this->assertNull($process->getPid());
    }

    public function testGetPidIsNullAfterRun()
    {
        $process = $this->getProcess('php -m');
        $process->run();
        $this->assertNull($process->getPid());
    }

    public function testSignal()
    {
        $this->verifyPosixIsEnabled();

        $process = $this->getProcess('exec php -f '.__DIR__.'/SignalListener.php');
        $process->start();
        usleep(500000);
        $process->signal(SIGUSR1);

        while ($process->isRunning() && false === strpos($process->getOutput(), 'Caught SIGUSR1')) {
            usleep(10000);
        }

        $this->assertEquals('Caught SIGUSR1', $process->getOutput());
    }

    public function testExitCodeIsAvailableAfterSignal()
    {
        $this->verifyPosixIsEnabled();

        $process = $this->getProcess('sleep 4');
        $process->start();
        $process->signal(SIGKILL);

        while ($process->isRunning()) {
            usleep(10000);
        }

        $this->assertFalse($process->isRunning());
        $this->assertTrue($process->hasBeenSignaled());
        $this->assertFalse($process->isSuccessful());
        $this->assertEquals(137, $process->getExitCode());
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\LogicException
     */
    public function testSignalProcessNotRunning()
    {
        $this->verifyPosixIsEnabled();
        $process = $this->getProcess('php -m');
        $process->signal(SIGHUP);
    }

    /**
     * @dataProvider provideMethodsThatNeedARunningProcess
     */
    public function testMethodsThatNeedARunningProcess($method)
    {
        $process = $this->getProcess('php -m');
        $this->setExpectedException('Symfony\Component\Process\Exception\LogicException', sprintf('Process must be started before calling %s.', $method));
        call_user_func(array($process, $method));
    }

    public function provideMethodsThatNeedARunningProcess()
    {
        return array(
            array('getOutput'),
            array('getIncrementalOutput'),
            array('getErrorOutput'),
            array('getIncrementalErrorOutput'),
            array('wait'),
        );
    }

    /**
     * @dataProvider provideMethodsThatNeedATerminatedProcess
     */
    public function testMethodsThatNeedATerminatedProcess($method)
    {
        $process = $this->getProcess('php -r "sleep(1);"');
        $process->start();
        try {
            call_user_func(array($process, $method));
            $process->stop(0);
            $this->fail('A LogicException must have been thrown');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Symfony\Component\Process\Exception\LogicException', $e);
            $this->assertEquals(sprintf('Process must be terminated before calling %s.', $method), $e->getMessage());
        }
        $process->stop(0);
    }

    public function provideMethodsThatNeedATerminatedProcess()
    {
        return array(
            array('hasBeenSignaled'),
            array('getTermSignal'),
            array('hasBeenStopped'),
            array('getStopSignal'),
        );
    }

    private function verifyPosixIsEnabled()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('POSIX signals do not work on Windows');
        }
        if (!defined('SIGUSR1')) {
            $this->markTestSkipped('The pcntl extension is not enabled');
        }
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     */
    public function testSignalWithWrongIntSignal()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('POSIX signals do not work on Windows');
        }

        $process = $this->getProcess('php -r "sleep(3);"');
        $process->start();
        $process->signal(-4);
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\RuntimeException
     */
    public function testSignalWithWrongNonIntSignal()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('POSIX signals do not work on Windows');
        }

        $process = $this->getProcess('php -r "sleep(3);"');
        $process->start();
        $process->signal('Céphalopodes');
    }

    public function responsesCodeProvider()
    {
        return array(
            //expected output / getter / code to execute
            //array(1,'getExitCode','exit(1);'),
            //array(true,'isSuccessful','exit();'),
            array('output', 'getOutput', 'echo \'output\';'),
        );
    }

    public function pipesCodeProvider()
    {
        $variations = array(
            'fwrite(STDOUT, $in = file_get_contents(\'php://stdin\')); fwrite(STDERR, $in);',
            'include \''.__DIR__.'/PipeStdinInStdoutStdErrStreamSelect.php\';',
        );

        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            // Avoid XL buffers on Windows because of https://bugs.php.net/bug.php?id=65650
            $sizes = array(1, 2, 4, 8);
        } else {
            $sizes = array(1, 16, 64, 1024, 4096);
        }

        $codes = array();
        foreach ($sizes as $size) {
            foreach ($variations as $code) {
                $codes[] = array($code, $size);
            }
        }

        return $codes;
    }

    /**
     * provides default method names for simple getter/setter
     */
    public function methodProvider()
    {
        $defaults = array(
            array('CommandLine'),
            array('Timeout'),
            array('WorkingDirectory'),
            array('Env'),
            array('Stdin'),
            array('Options'),
        );

        return $defaults;
    }

    /**
     * @param string  $commandline
     * @param null    $cwd
     * @param array   $env
     * @param null    $stdin
     * @param int     $timeout
     * @param array   $options
     *
     * @return Process
     */
    abstract protected function getProcess($commandline, $cwd = null, array $env = null, $stdin = null, $timeout = 60, array $options = array());
}

class Stringifiable
{
    public function __toString()
    {
        return 'stringifiable';
    }
}

class NonStringifiable
{
}
