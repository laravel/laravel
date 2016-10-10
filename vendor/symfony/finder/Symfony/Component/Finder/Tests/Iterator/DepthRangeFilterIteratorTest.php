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

use Symfony\Component\Finder\Iterator\DepthRangeFilterIterator;

class DepthRangeFilterIteratorTest extends RealIteratorTestCase
{
    /**
     * @dataProvider getAcceptData
     */
    public function testAccept($minDepth, $maxDepth, $expected)
    {
        $inner = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->toAbsolute(), \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);

        $iterator = new DepthRangeFilterIterator($inner, $minDepth, $maxDepth);

        $actual = array_keys(iterator_to_array($iterator));
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    public function getAcceptData()
    {
        $lessThan1 = array(
            '.git',
            'test.py',
            'foo',
            'test.php',
            'toto',
            '.foo',
            '.bar',
            'foo bar',
        );

        $lessThanOrEqualTo1 = array(
            '.git',
            'test.py',
            'foo',
            'foo/bar.tmp',
            'test.php',
            'toto',
            '.foo',
            '.foo/.bar',
            '.bar',
            'foo bar',
            '.foo/bar',
        );

        $graterThanOrEqualTo1 = array(
            'foo/bar.tmp',
            '.foo/.bar',
            '.foo/bar',
        );

        $equalTo1 = array(
            'foo/bar.tmp',
            '.foo/.bar',
            '.foo/bar',
        );

        return array(
            array(0, 0, $this->toAbsolute($lessThan1)),
            array(0, 1, $this->toAbsolute($lessThanOrEqualTo1)),
            array(2, PHP_INT_MAX, array()),
            array(1, PHP_INT_MAX, $this->toAbsolute($graterThanOrEqualTo1)),
            array(1, 1, $this->toAbsolute($equalTo1)),
        );
    }
}
