<?php

/*
 * This file is part of the Symfony package.
*
* (c) Fabien Potencier <fabien@symfony.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Symfony\Component\Finder\Tests\Iterator;

use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

class RecursiveDirectoryIteratorTest extends IteratorTestCase
{
    /**
     * @dataProvider getPaths
     *
     * @param string  $path
     * @param bool    $seekable
     * @param array   $contains
     * @param string  $message
     */
    public function testRewind($path, $seekable, $contains, $message = null)
    {
        try {
            $i = new RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        } catch (\UnexpectedValueException $e) {
            $this->markTestSkipped(sprintf('Unsupported stream "%s".', $path));
        }

        $i->rewind();

        $this->assertTrue(true, $message);
    }

    /**
     * @dataProvider getPaths
     *
     * @param string  $path
     * @param bool    $seekable
     * @param array   $contains
     * @param string  $message
     */
    public function testSeek($path, $seekable, $contains, $message = null)
    {
        try {
            $i = new RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        } catch (\UnexpectedValueException $e) {
            $this->markTestSkipped(sprintf('Unsupported stream "%s".', $path));
        }

        $actual = array();

        $i->seek(0);
        $actual[] = $i->getPathname();

        $i->seek(1);
        $actual[] = $i->getPathname();

        $i->seek(2);
        $actual[] = $i->getPathname();

        $this->assertEquals($contains, $actual);
    }

    public function getPaths()
    {
        $data = array();

        // ftp
        $contains = array(
            'ftp://ftp.mozilla.org'.DIRECTORY_SEPARATOR.'README',
            'ftp://ftp.mozilla.org'.DIRECTORY_SEPARATOR.'index.html',
            'ftp://ftp.mozilla.org'.DIRECTORY_SEPARATOR.'pub',
        );
        $data[] = array('ftp://ftp.mozilla.org/', false, $contains);

        return $data;
    }
}
