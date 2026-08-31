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

/**
 * Interface to describe UIDs that contain a DateTimeImmutable as part of their behaviour.
 *
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
interface TimeBasedUidInterface
{
    public function getDateTime(): \DateTimeImmutable;
}
