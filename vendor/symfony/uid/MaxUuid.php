<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Uid;

class MaxUuid extends Uuid
{
    protected const TYPE = -1;

    public function __construct()
    {
        $this->uid = parent::MAX;
    }
}
