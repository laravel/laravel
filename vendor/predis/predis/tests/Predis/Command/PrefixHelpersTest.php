<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Command;

use PredisTestCase;

/**
 *
 */
class PrefixHelpersTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testPrefixFirst()
    {
        $arguments = array('1st', '2nd', '3rd', '4th');
        $expected = array('prefix:1st', '2nd', '3rd', '4th');

        $command = $this->getMockForAbstractClass('Predis\Command\AbstractCommand');
        $command->setRawArguments($arguments);

        PrefixHelpers::first($command, 'prefix:');

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testPrefixAll()
    {
        $arguments = array('1st', '2nd', '3rd', '4th');
        $expected = array('prefix:1st', 'prefix:2nd', 'prefix:3rd', 'prefix:4th');

        $command = $this->getMockForAbstractClass('Predis\Command\AbstractCommand');
        $command->setRawArguments($arguments);

        PrefixHelpers::all($command, 'prefix:');

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testPrefixInterleaved()
    {
        $arguments = array('1st', '2nd', '3rd', '4th');
        $expected = array('prefix:1st', '2nd', 'prefix:3rd', '4th');

        $command = $this->getMockForAbstractClass('Predis\Command\AbstractCommand');
        $command->setRawArguments($arguments);

        PrefixHelpers::interleaved($command, 'prefix:');

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testPrefixSkipLast()
    {
        $arguments = array('1st', '2nd', '3rd', '4th');
        $expected = array('prefix:1st', 'prefix:2nd', 'prefix:3rd', '4th');

        $command = $this->getMockForAbstractClass('Predis\Command\AbstractCommand');
        $command->setRawArguments($arguments);

        PrefixHelpers::skipLast($command, 'prefix:');

        $this->assertSame($expected, $command->getArguments());
    }
}
