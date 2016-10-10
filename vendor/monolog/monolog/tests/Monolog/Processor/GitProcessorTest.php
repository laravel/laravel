<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Processor;

use Monolog\TestCase;

class GitProcessorTest extends TestCase
{
    /**
     * @covers Monolog\Processor\GitProcessor::__invoke
     */
    public function testProcessor()
    {
        $processor = new GitProcessor();
        $record = $processor($this->getRecord());

        $this->assertArrayHasKey('git', $record['extra']);
        $this->assertTrue(!is_array($record['extra']['git']['branch']));
    }
}
