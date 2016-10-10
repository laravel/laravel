<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Iterator;

/**
 * This iterator just overrides the rewind method in order to correct a PHP bug.
 *
 * @see https://bugs.php.net/bug.php?id=49104
 *
 * @author Alex Bogomazov
 */
abstract class FilterIterator extends \FilterIterator
{
    /**
     * This is a workaround for the problem with \FilterIterator leaving inner \FilesystemIterator in wrong state after
     * rewind in some cases.
     *
     * @see FilterIterator::rewind()
     */
    public function rewind()
    {
        $iterator = $this;
        while ($iterator instanceof \OuterIterator) {
            $innerIterator = $iterator->getInnerIterator();

            if ($innerIterator instanceof RecursiveDirectoryIterator) {
                if ($innerIterator->isRewindable()) {
                    $innerIterator->next();
                    $innerIterator->rewind();
                }
            } elseif ($iterator->getInnerIterator() instanceof \FilesystemIterator) {
                $iterator->getInnerIterator()->next();
                $iterator->getInnerIterator()->rewind();
            }
            $iterator = $iterator->getInnerIterator();
        }

        parent::rewind();
    }
}
