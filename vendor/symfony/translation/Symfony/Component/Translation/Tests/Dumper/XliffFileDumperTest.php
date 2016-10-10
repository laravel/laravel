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
use Symfony\Component\Translation\Dumper\XliffFileDumper;

class XliffFileDumperTest extends \PHPUnit_Framework_TestCase
{
    public function testDump()
    {
        $catalogue = new MessageCatalogue('en');
        $catalogue->add(array(
            'foo'            => 'bar',
            'key'            => '',
            'key.with.cdata' => '<source> & <target>',
        ));

        $tempDir = sys_get_temp_dir();
        $dumper = new XliffFileDumper();
        $dumper->dump($catalogue, array('path' => $tempDir));

        $this->assertSame(
            file_get_contents(__DIR__.'/../fixtures/resources-clean.xlf'),
            file_get_contents($tempDir.'/messages.en.xlf')
        );

        unlink($tempDir.'/messages.en.xlf');
    }
}
