<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Tests\Dumper;

use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Dumper\JsonFileDumper;

class JsonFileDumperTest extends \PHPUnit_Framework_TestCase
{
    public function testDump()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestIncomplete('PHP below 5.4 doesn\'t support JSON pretty printing');
        }

        $catalogue = new MessageCatalogue('en');
        $catalogue->add(array('foo' => 'bar'));

        $tempDir = sys_get_temp_dir();
        $dumper = new JsonFileDumper();
        $dumper->dump($catalogue, array('path' => $tempDir));

        $this->assertEquals(file_get_contents(__DIR__.'/../fixtures/resources.json'), file_get_contents($tempDir.'/messages.en.json'));

        unlink($tempDir.'/messages.en.json');
    }
}
