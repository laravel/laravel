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

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\Output;

class ConsoleOutputTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $output = new ConsoleOutput(Output::VERBOSITY_QUIET, true);
        $this->assertEquals(Output::VERBOSITY_QUIET, $output->getVerbosity(), '__construct() takes the verbosity as its first argument');
        $this->assertSame($output->getFormatter(), $output->getErrorOutput()->getFormatter(), '__construct() takes a formatter or null as the third argument');
    }
}
