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

class MemoryPeakUsageProcessorTest extends TestCase
{
    /**
     * @covers Monolog\Processor\MemoryPeakUsageProcessor::__invoke
     * @covers Monolog\Processor\MemoryProcessor::formatBytes
     */
    public function testProcessor()
    {
        $processor = new MemoryPeakUsageProcessor();
        $record = $processor($this->getRecord());
        $this->assertArrayHasKey('memory_peak_usage', $record['extra']);
        $this->assertRegExp('#[0-9.]+ (M|K)?B$#', $record['extra']['memory_peak_usage']);
    }

    /**
     * @covers Monolog\Processor\MemoryPeakUsageProcessor::__invoke
     * @covers Monolog\Processor\MemoryProcessor::formatBytes
     */
    public function testProcessorWithoutFormatting()
    {
        $processor = new MemoryPeakUsageProcessor(true, false);
        $record = $processor($this->getRecord());
        $this->assertArrayHasKey('memory_peak_usage', $record['extra']);
        $this->assertInternalType('int', $record['extra']['memory_peak_usage']);
        $this->assertGreaterThan(0, $record['extra']['memory_peak_usage']);
    }
}
