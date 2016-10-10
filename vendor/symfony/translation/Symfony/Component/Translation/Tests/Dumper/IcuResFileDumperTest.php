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
use Symfony\Component\Translation\Dumper\IcuResFileDumper;

class IcuResFileDumperTest extends \PHPUnit_Framework_TestCase
{
    public function testDump()
    {
        if (!function_exists('mb_convert_encoding')) {
            $this->markTestSkipped('This test requires mbstring to work.');
        }

        $catalogue = new MessageCatalogue('en');
        $catalogue->add(array('foo' => 'bar'));

        $tempDir = sys_get_temp_dir().'/IcuResFileDumperTest';
        mkdir($tempDir);
        $dumper = new IcuResFileDumper();
        $dumper->dump($catalogue, array('path' => $tempDir));

        $this->assertEquals(file_get_contents(__DIR__.'/../fixtures/resourcebundle/res/en.res'), file_get_contents($tempDir.'/messages/en.res'));

        unlink($tempDir.'/messages/en.res');
        rmdir($tempDir.'/messages');
        rmdir($tempDir);
    }
}
