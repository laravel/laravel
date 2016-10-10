<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Output;

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class NullOutputTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $output = new NullOutput();

        ob_start();
        $output->write('foo');
        $buffer = ob_get_clean();

        $this->assertSame('', $buffer, '->write() does nothing (at least nothing is printed)');
        $this->assertFalse($output->isDecorated(), '->isDecorated() returns false');
    }

    public function testVerbosity()
    {
        $output = new NullOutput();
        $this->assertSame(OutputInterface::VERBOSITY_QUIET, $output->getVerbosity(), '->getVerbosity() returns VERBOSITY_QUIET for NullOutput by default');

        $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
        $this->assertSame(OutputInterface::VERBOSITY_QUIET, $output->getVerbosity(), '->getVerbosity() always returns VERBOSITY_QUIET for NullOutput');
    }
}
