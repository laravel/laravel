<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Tests\FakeAdapter;

use Symfony\Component\Finder\Adapter\AbstractAdapter;
use Symfony\Component\Finder\Exception\AdapterFailureException;

/**
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class FailingAdapter extends AbstractAdapter
{
    /**
     * {@inheritdoc}
     */
    public function searchInDirectory($dir)
    {
        throw new AdapterFailureException($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'failing';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeUsed()
    {
        return true;
    }
}
