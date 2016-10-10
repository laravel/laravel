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

use Symfony\Component\Process\ProcessBuilder;

class ProcessBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testInheritEnvironmentVars()
    {
        $_ENV['MY_VAR_1'] = 'foo';

        $proc = ProcessBuilder::create()
            ->add('foo')
            ->getProcess();

        unset($_ENV['MY_VAR_1']);

        $env = $proc->getEnv();
        $this->assertArrayHasKey('MY_VAR_1', $env);
        $this->assertEquals('foo', $env['MY_VAR_1']);
    }

    public function testAddEnvironmentVariables()
    {
        $pb = new ProcessBuilder();
        $env = array(
            'foo' => 'bar',
            'foo2' => 'bar2',
        );
        $proc = $pb
            ->add('command')
            ->setEnv('foo', 'bar2')
            ->addEnvironmentVariables($env)
            ->inheritEnvironmentVariables(false)
            ->getProcess()
        ;

        $this->assertSame($env, $proc->getEnv());
    }

    public function testProcessShouldInheritAndOverrideEnvironmentVars()
    {
        $_ENV['MY_VAR_1'] = 'foo';

        $proc = ProcessBuilder::create()
            ->setEnv('MY_VAR_1', 'bar')
            ->add('foo')
            ->getProcess();

        unset($_ENV['MY_VAR_1']);

        $env = $proc->getEnv();
        $this->assertArrayHasKey('MY_VAR_1', $env);
        $this->assertEquals('bar', $env['MY_VAR_1']);
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\InvalidArgumentException
     */
    public function testNegativeTimeoutFromSetter()
    {
        $pb = new ProcessBuilder();
        $pb->setTimeout(-1);
    }

    public function testNullTimeout()
    {
        $pb = new ProcessBuilder();
        $pb->setTimeout(10);
        $pb->setTimeout(null);

        $r = new \ReflectionObject($pb);
        $p = $r->getProperty('timeout');
        $p->setAccessible(true);

        $this->assertNull($p->getValue($pb));
    }

    public function testShouldSetArguments()
    {
        $pb = new ProcessBuilder(array('initial'));
        $pb->setArguments(array('second'));

        $proc = $pb->getProcess();

        $this->assertContains("second", $proc->getCommandLine());
    }

    public function testPrefixIsPrependedToAllGeneratedProcess()
    {
        $pb = new ProcessBuilder();
        $pb->setPrefix('/usr/bin/php');

        $proc = $pb->setArguments(array('-v'))->getProcess();
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->assertEquals('"/usr/bin/php" "-v"', $proc->getCommandLine());
        } else {
            $this->assertEquals("'/usr/bin/php' '-v'", $proc->getCommandLine());
        }

        $proc = $pb->setArguments(array('-i'))->getProcess();
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->assertEquals('"/usr/bin/php" "-i"', $proc->getCommandLine());
        } else {
            $this->assertEquals("'/usr/bin/php' '-i'", $proc->getCommandLine());
        }
    }

    public function testArrayPrefixesArePrependedToAllGeneratedProcess()
    {
        $pb = new ProcessBuilder();
        $pb->setPrefix(array('/usr/bin/php', 'composer.phar'));

        $proc = $pb->setArguments(array('-v'))->getProcess();
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->assertEquals('"/usr/bin/php" "composer.phar" "-v"', $proc->getCommandLine());
        } else {
            $this->assertEquals("'/usr/bin/php' 'composer.phar' '-v'", $proc->getCommandLine());
        }

        $proc = $pb->setArguments(array('-i'))->getProcess();
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->assertEquals('"/usr/bin/php" "composer.phar" "-i"', $proc->getCommandLine());
        } else {
            $this->assertEquals("'/usr/bin/php' 'composer.phar' '-i'", $proc->getCommandLine());
        }
    }

    public function testShouldEscapeArguments()
    {
        $pb = new ProcessBuilder(array('%path%', 'foo " bar', '%baz%baz'));
        $proc = $pb->getProcess();

        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->assertSame('^%"path"^% "foo \\" bar" "%baz%baz"', $proc->getCommandLine());
        } else {
            $this->assertSame("'%path%' 'foo \" bar' '%baz%baz'", $proc->getCommandLine());
        }
    }

    public function testShouldEscapeArgumentsAndPrefix()
    {
        $pb = new ProcessBuilder(array('arg'));
        $pb->setPrefix('%prefix%');
        $proc = $pb->getProcess();

        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->assertSame('^%"prefix"^% "arg"', $proc->getCommandLine());
        } else {
            $this->assertSame("'%prefix%' 'arg'", $proc->getCommandLine());
        }
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\LogicException
     */
    public function testShouldThrowALogicExceptionIfNoPrefixAndNoArgument()
    {
        ProcessBuilder::create()->getProcess();
    }

    public function testShouldNotThrowALogicExceptionIfNoArgument()
    {
        $process = ProcessBuilder::create()
            ->setPrefix('/usr/bin/php')
            ->getProcess();

        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->assertEquals('"/usr/bin/php"', $process->getCommandLine());
        } else {
            $this->assertEquals("'/usr/bin/php'", $process->getCommandLine());
        }
    }

    public function testShouldNotThrowALogicExceptionIfNoPrefix()
    {
        $process = ProcessBuilder::create(array('/usr/bin/php'))
            ->getProcess();

        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->assertEquals('"/usr/bin/php"', $process->getCommandLine());
        } else {
            $this->assertEquals("'/usr/bin/php'", $process->getCommandLine());
        }
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\InvalidArgumentException
     * @expectedExceptionMessage Symfony\Component\Process\ProcessBuilder::setInput only accepts strings.
     */
    public function testInvalidInput()
    {
        $builder = ProcessBuilder::create();
        $builder->setInput(array());
    }
}
