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

use Symfony\Component\Finder\Iterator\FilecontentFilterIterator;

class FilecontentFilterIteratorTest extends IteratorTestCase
{
    public function testAccept()
    {
        $inner = new MockFileListIterator(array('test.txt'));
        $iterator = new FilecontentFilterIterator($inner, array(), array());
        $this->assertIterator(array('test.txt'), $iterator);
    }

    public function testDirectory()
    {
        $inner = new MockFileListIterator(array('directory'));
        $iterator = new FilecontentFilterIterator($inner, array('directory'), array());
        $this->assertIterator(array(), $iterator);
    }

    public function testUnreadableFile()
    {
        $inner = new MockFileListIterator(array('file r-'));
        $iterator = new FilecontentFilterIterator($inner, array('file r-'), array());
        $this->assertIterator(array(), $iterator);
    }

    /**
     * @dataProvider getTestFilterData
     */
    public function testFilter(\Iterator $inner, array $matchPatterns, array $noMatchPatterns, array $resultArray)
    {
        $iterator = new FilecontentFilterIterator($inner, $matchPatterns, $noMatchPatterns);
        $this->assertIterator($resultArray, $iterator);
    }

    public function getTestFilterData()
    {
        $inner = new MockFileListIterator();

        $inner[] = new MockSplFileInfo(array(
            'name'     => 'a.txt',
            'contents' => 'Lorem ipsum...',
            'type'     => 'file',
            'mode'     => 'r+',)
        );

        $inner[] = new MockSplFileInfo(array(
            'name'     => 'b.yml',
            'contents' => 'dolor sit...',
            'type'     => 'file',
            'mode'     => 'r+',)
        );

        $inner[] = new MockSplFileInfo(array(
            'name'     => 'some/other/dir/third.php',
            'contents' => 'amet...',
            'type'     => 'file',
            'mode'     => 'r+',)
        );

        $inner[] = new MockSplFileInfo(array(
            'name'     => 'unreadable-file.txt',
            'contents' => false,
            'type'     => 'file',
            'mode'     => 'r+',)
        );

        return array(
            array($inner, array('.'), array(), array('a.txt', 'b.yml', 'some/other/dir/third.php')),
            array($inner, array('ipsum'), array(), array('a.txt')),
            array($inner, array('i', 'amet'), array('Lorem', 'amet'), array('b.yml')),
        );
    }
}
