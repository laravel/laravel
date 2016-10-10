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

/**
 * @author Alex Bogomazov
 */
class FilterIteratorTest extends RealIteratorTestCase
{
    public function testFilterFilesystemIterators()
    {
        $i = new \FilesystemIterator($this->toAbsolute());

        // it is expected that there are test.py test.php in the tmpDir
        $i = $this->getMockForAbstractClass('Symfony\Component\Finder\Iterator\FilterIterator', array($i));
        $i->expects($this->any())
            ->method('accept')
            ->will($this->returnCallback(function () use ($i) {
                return (bool) preg_match('/\.php/', (string) $i->current());
            })
        );

        $c = 0;
        foreach ($i as $item) {
            $c++;
        }

        $this->assertEquals(1, $c);

        $i->rewind();

        $c = 0;
        foreach ($i as $item) {
            $c++;
        }

        // This would fail with \FilterIterator but works with Symfony\Component\Finder\Iterator\FilterIterator
        // see https://bugs.php.net/bug.php?id=49104
        $this->assertEquals(1, $c);
    }
}
