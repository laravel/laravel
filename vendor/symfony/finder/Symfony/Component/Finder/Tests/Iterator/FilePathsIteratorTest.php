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

use Symfony\Component\Finder\Iterator\FilePathsIterator;

class FilePathsIteratorTest extends RealIteratorTestCase
{
    /**
     * @dataProvider getSubPathData
     */
    public function testSubPath($baseDir, array $paths, array $subPaths, array $subPathnames)
    {
        $iterator = new FilePathsIterator($paths, $baseDir);

        foreach ($iterator as $index => $file) {
            $this->assertEquals($paths[$index], $file->getPathname());
            $this->assertEquals($subPaths[$index], $iterator->getSubPath());
            $this->assertEquals($subPathnames[$index], $iterator->getSubPathname());
        }
    }

    public function getSubPathData()
    {
        $tmpDir = sys_get_temp_dir().'/symfony2_finder';

        return array(
            array(
                $tmpDir,
                array( // paths
                    $tmpDir.DIRECTORY_SEPARATOR.'.git' => $tmpDir.DIRECTORY_SEPARATOR.'.git',
                    $tmpDir.DIRECTORY_SEPARATOR.'test.py' => $tmpDir.DIRECTORY_SEPARATOR.'test.py',
                    $tmpDir.DIRECTORY_SEPARATOR.'foo' => $tmpDir.DIRECTORY_SEPARATOR.'foo',
                    $tmpDir.DIRECTORY_SEPARATOR.'foo'.DIRECTORY_SEPARATOR.'bar.tmp' => $tmpDir.DIRECTORY_SEPARATOR.'foo'.DIRECTORY_SEPARATOR.'bar.tmp',
                    $tmpDir.DIRECTORY_SEPARATOR.'test.php' => $tmpDir.DIRECTORY_SEPARATOR.'test.php',
                    $tmpDir.DIRECTORY_SEPARATOR.'toto' => $tmpDir.DIRECTORY_SEPARATOR.'toto',
                ),
                array( // subPaths
                    $tmpDir.DIRECTORY_SEPARATOR.'.git' => '',
                    $tmpDir.DIRECTORY_SEPARATOR.'test.py' => '',
                    $tmpDir.DIRECTORY_SEPARATOR.'foo' => '',
                    $tmpDir.DIRECTORY_SEPARATOR.'foo'.DIRECTORY_SEPARATOR.'bar.tmp' => 'foo',
                    $tmpDir.DIRECTORY_SEPARATOR.'test.php' => '',
                    $tmpDir.DIRECTORY_SEPARATOR.'toto' => '',
                ),
                array( // subPathnames
                    $tmpDir.DIRECTORY_SEPARATOR.'.git' => '.git',
                    $tmpDir.DIRECTORY_SEPARATOR.'test.py' => 'test.py',
                    $tmpDir.DIRECTORY_SEPARATOR.'foo' => 'foo',
                    $tmpDir.DIRECTORY_SEPARATOR.'foo'.DIRECTORY_SEPARATOR.'bar.tmp' => 'foo'.DIRECTORY_SEPARATOR.'bar.tmp',
                    $tmpDir.DIRECTORY_SEPARATOR.'test.php' => 'test.php',
                    $tmpDir.DIRECTORY_SEPARATOR.'toto' => 'toto',
                ),
            ),
        );
    }
}
